<?php

namespace Chameloun\PHPDockerClient;

use Chameloun\PHPDockerClient\DockerConfig\HttpMethodEnum;
use Chameloun\PHPDockerClient\DockerError\DockerException;
use Chameloun\PHPDockerClient\DockerError\DockerErrorCodeEnum;
use PHPUnit\Util\Json;

/**
 * 
 * @class DockerClient
 * 
 */

final class DockerClient {

    # UNIX socket

    /**
     * @var string
     */
    private string $unix_socket = "/var/run/docker.sock";

    # API

    /**
     * @var string
     */
    private string $hostname;

    /**
     * @var int
     */
    private int $port;


    /**
     * @var bool
     */
    private bool $use_socket;

    /**
     * @var string|null
     */
    private string | null $working_container_id_name = null;

    /**
     * 
     * @param bool $use_socket
     * @param string $hostname
     * @param int $port
     * 
     */
    public function __construct( bool $use_socket = true, string $hostname = "localhost", int $port = 2375)
    {
        
        $this->use_socket = $use_socket;

        if (!$use_socket) {

            $this->hostname = $hostname;
            $this->port = $port;

        }

    }

    /**
     * 
     * @param string $unix_socket
     * 
     */
    public function setUnixSocket( string $unix_socket ) : void {

        $this->unix_socket = $unix_socket;

    }

    /**
     * @param HttpMethodEnum $method
     * @param string $path
     * @param string $data
     * @param array $headers
     * @param array $allowed_codes
     * @return object
     * @throws DockerException
     */
    private function dockerApiRequest(HttpMethodEnum $method, string $path, string $data = "", array $headers = array(), array $allowed_codes = array(200, 201, 202, 204)) : object {

        $curl = curl_init();

        if ($this->use_socket) {

            $curl_config = array(
                CURLOPT_UNIX_SOCKET_PATH => $this->unix_socket,
                CURLOPT_URL => 'http://localhost' . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method->value,
            );

        } else {

            $curl_config = array(
                CURLOPT_URL => 'http://'. $this->hostname .':'. $this->port . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method->value,
            );

        }

        if (empty($headers)) {

            $curl_config[CURLOPT_HTTPHEADER] = $headers;

        }

        if ($method === HttpMethodEnum::POST and $data !== "") {

            $curl_config[CURLOPT_POSTFIELDS] = $data;
            $curl_config[CURLOPT_HTTPHEADER] = array('Accept: application/json', 'Content-Type: application/json');

        }

        curl_setopt_array($curl, $curl_config);
        
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        if (!curl_errno($curl)) {

            curl_close($curl);

            if (!in_array($info['http_code'], $allowed_codes, true)) {

                try {

                    $message = json_decode($response, false, 512, JSON_THROW_ON_ERROR)->message . " HTTP Code: " . $info['http_code'];

                } catch (\JsonException $e) {

                    $message = "Request to Docker API failed! HTTP Code: " . $info['http_code'] . " Response: " . rtrim($response, PHP_EOL) . ".";
                    
                }
                
                throw new DockerException($message, DockerErrorCodeEnum::ERROR);

            }

            try {

                $message = (object)json_decode($response, false, 512, JSON_THROW_ON_ERROR);

            } catch (\JsonException $e) {

                $message = (object)array("message" => $response);

            }

            return $message;

        }

        throw new DockerException("Request to Docker API failed!", DockerErrorCodeEnum::ERROR);

    }

    /**
     * @param string $logs
     * @return string
     */
    private function formatContainerLogs(string $logs): string {

        $logs = preg_replace('/[\x00\x02\x1c\x01\x1e]/', '', $logs);

        $lines = explode("\n", $logs);

        for ($i = 1, $iMax = count($lines); $i < $iMax; $i++) {
            $lines[$i] = substr($lines[$i], 1);
        }
        unset($line);

        return implode("\n", $lines);

    }
    public function listContainers(bool $all = false, ?string $filter = null) : array {

        try {

            $containers = array();

            $containers_response = $this->dockerApiRequest(HttpMethodEnum::GET, "/containers/json?all=" . $all . (($filter) ? "&filters=" . $filter : ""), allowed_codes: array(200));

            foreach ($containers_response as $container) {

                $containers[] = new DockerContainer($container);

            }

            return $containers;

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function waitForContainer( ?string $id_name = null ) : void {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . ($id_name ?? $this->getWorkingContainer()) . "/wait", allowed_codes: array(200));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function listContainersId(bool $running = false) : array {

        $containers = $this->listContainers($running);
        $ids = array();

        foreach ($containers as $container) {

            $ids[] = $container->getId();

        }

        return $ids;

    }
    public function listContainersName(bool $running = false) : array {

        $containers = $this->listContainers($running);
        $names = array();

        foreach ($containers as $container) {

            $names[] = $container->getName();

        }

        return $names;

    }
    public function getContainer( ?string $id_name = null ) {

        try {

            $container = $this->dockerApiRequest(HttpMethodEnum::GET, "/containers/" . ($id_name ?? $this->getWorkingContainer()) . "/json", allowed_codes: array(200));
            return new DockerContainer($container);

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function stopContainer( ?string $id_name = null ) : void {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . ($id_name ?? $this->getWorkingContainer()) . "/stop", allowed_codes: array(204, 304));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function stopAllContainers() : void {

        $ids = $this->listContainersId();

        foreach ($ids as $id) {

            $this->stopContainer($id);
            $this->waitForContainer($id);

        }

    }
    public function startContainer( ?string $id_name = null ) : void {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . ($id_name ?? $this->getWorkingContainer()) . "/start", allowed_codes: array(204, 304));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function startAllContainers() : void {

        $ids = $this->listContainersId(true);

        foreach ($ids as $id) {

            $this->startContainer($id);

        }

    }
    public function restartContainer( string $id ) : void {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . $id . "/restart", allowed_codes: array(204));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function restartAllContainers(bool $all = false) : void {

        $ids = $this->listContainersId($all);

        foreach ($ids as $id) {

            $this->restartContainer($id);

        }

    }
    public function createContainer(DockerContainerConfig $config) : string {

        try {

            $container_config = $config->createRequestBody();

            $this->setWorkingContainer($config->getName());

        } catch (\JsonException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

        try {

            return ($this->dockerApiRequest(HttpMethodEnum::POST, '/containers/create?name=' . $config->getName(), $container_config, allowed_codes: array(201)))->Id;

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function getContainerLogs(?string $id_name = null, bool $raw = false): string
    {

        try {

            $logs = ($this->dockerApiRequest(HttpMethodEnum::GET, '/containers/' . ($id_name ?? $this->getWorkingContainer()) . '/logs?stdout=true', allowed_codes: array(200)))->message;

            return $raw ? $logs : $this->formatContainerLogs($logs ?? "");

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function getContainerErrorLogs(?string $id_name = null, bool $raw = false): string
    {

        try {

            $logs = ($this->dockerApiRequest(HttpMethodEnum::GET, '/containers/' . ($id_name ?? $this->getWorkingContainer()) . '/logs?stderr=true', allowed_codes: array(200)))->message;

            return $raw ? $logs : $this->formatContainerLogs($logs ?? "");

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function removeContainer(?string $id_name = null, bool $delete_volumes = false, bool $force = false, bool $delete_link = false) : object {

        try {

            return $this->dockerApiRequest(HttpMethodEnum::DELETE, '/containers/' . ($id_name ?? $this->getWorkingContainer()) . "?v=" . $delete_volumes . "&force=" . $force . "&link=" . $delete_link, allowed_codes: array(204));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function pullImage(string $image, string $tag ) : object {

        try {

            return $this->dockerApiRequest(HttpMethodEnum::POST, "/images/create?fromImage=" . $image . "&tag=" . $tag, allowed_codes: array(200));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function listAllImages() : array {

        try {

            $images_response = $this->dockerApiRequest(HttpMethodEnum::GET, "/images/json", allowed_codes: array(200));
            $images = array();

            foreach ($images_response as $image) {

                $images[] = new DockerImage($image);

            }

            return $images;

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function getImage(string $name, string $tag ) : DockerImage {

        try {

            $image = (array)$this->dockerApiRequest(HttpMethodEnum::GET, '/images/json?filters={"reference":["' . $name . ':' . $tag .'"]}', allowed_codes: array(200));

            if (empty($image)) {
                throw new DockerException("Image not found!", DockerErrorCodeEnum::WARNING);
            }

            return new DockerImage(($image)[0]);

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }

    /**
     * @param string $id_name
     * @return $this
     */
    public function setWorkingContainer(string $id_name) {

        $this->working_container_id_name = $id_name;

        return $this;

    }

    /**
     * @return bool
     */
    public function isWorkingContainerSet() {

        return !empty($this->working_container_id_name);

    }

    /**
     * @return string|null
     */
    public function getWorkingContainer() {

        return $this->working_container_id_name;

    }

    /**
     * @return $this
     */
    public function unsetWorkingContainer() {

        $this->working_container_id_name = null;

        return $this;

    }


}
