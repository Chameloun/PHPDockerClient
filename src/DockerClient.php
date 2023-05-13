<?php

namespace LocuTeam\PHPDockerClient;

use LocuTeam\PHPDockerClient\DockerConfig\HttpMethodEnum;

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
    public function setUnixSocket( string $unix_socket ) {

        $this->unix_socket = $unix_socket;

    }

    /**
     *
     * @param HttpMethodEnum $method
     * @param string $path
     * @param array $data
     * @param array $allowed_codes
     *
     * @return any
     *
     */
    private function dockerApiRequest(HttpMethodEnum $method, string $path, string $data = "", array $headers = array(), array $allowed_codes = array(200, 201, 202, 204)) {

        $curl = curl_init();

        $curl_config = array();

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

                throw new \ErrorException(json_decode($response, false)->message . " HTTP Code: " . $info['http_code'], 1, 1);

            }

            return json_decode($response, false) ?? $response;

        } else {

            throw new \ErrorException("Request to Docker API failed!", 1, 1);

        }

    }

    private function formatContainerLogs( string $logs ) {

        $logs = preg_replace('/[\x00\x02\x1c\x01\x1e]/', '', $logs);

        $lines = explode("\n", $logs);

        for ($i = 1, $iMax = count($lines); $i < $iMax; $i++) {
            $lines[$i] = substr($lines[$i], 1);
        }
        unset($line);

        return implode("\n", $lines);

    }

    /**
     * 
     * @param bool $running
     * 
     * 
     */
    public function listContainers(bool $running = false) {

        $containers = array();

        $containers_response = $this->dockerApiRequest(HttpMethodEnum::GET, "/containers/json?all=" . $running, allowed_codes: array(200));

        foreach ($containers_response as $container) {

            $containers[] = new DockerContainer($container);

        }

        return $containers;

    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    public function waitForContainer( string $id_name ) : bool {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . $id_name . "/wait", allowed_codes: array(200));
            return true;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * 
     * @param bool $running
     * 
     * @return array
     * 
     */
    public function listContainersId(bool $running = false) : array {

        $containers = $this->listContainers($running);
        $ids = array();

        foreach ($containers as $container) {

            $ids[] = $container->getId();

        }

        return $ids;

    }

    /**
     * 
     * @param bool $running
     * 
     * @return array
     * 
     */
    public function listContainersName(bool $running = false) : array {

        $containers = $this->listContainers($running);
        $names = array();

        foreach ($containers as $container) {

            $names[] = $container->getName();

        }

        return $names;

    }

    /**
     * 
     * @param string $id_name
     * 
     * 
     */
    public function getContainer( string $id_name ) {

        try {

            $container = $this->dockerApiRequest(HttpMethodEnum::GET, "/containers/" . $id_name . "/json", allowed_codes: array(200));
            return new DockerContainer((object)$container);

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    public function stopContainer( string $id ) : bool {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . $id . "/stop", allowed_codes: array(204, 304));
            return true;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * 
     * @return bool
     * 
     */
    public function stopAllContainers() : bool {

        $ids = $this->listContainersId();
        $success = true;

        foreach ($ids as $id) {

            $output = $this->stopContainer($id);
            $wait = $this->waitForContainer($id);

            if ((!$output) or (!$wait)) $success = false;

        }

        return $success;

    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    public function startContainer( string $id_name ) : bool {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . $id_name . "/start", allowed_codes: array(204, 304));
            return true;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * 
     * @return bool
     * 
     */
    public function startAllContainers() : bool {

        $ids = $this->listContainersId(true);
        $success = true;

        foreach ($ids as $id) {

            $output = $this->startContainer($id);

            if (!$output) $success = false;

        }

        return $success;

    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    public function restartContainer( string $id ) : bool {

        try {

            $this->dockerApiRequest(HttpMethodEnum::POST, "/containers/" . $id . "/restart", allowed_codes: array(204));
            return true;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * 
     * 
     * @return bool
     * 
     */
    public function restartAllContainers(bool $running = false) : bool {

        $ids = $this->listContainersId($running);
        $success = true;

        foreach ($ids as $id) {

            $output = $this->restartContainer($id);

            if (!$output) $success = false;

        }

        return $success;

    }

    /**
     * @param DockerContainerConfig $config
     * @return false
     * @throws \JsonException
     */
    public function createContainer(DockerContainerConfig $config) {

        $container_config = $config->createRequestBody();

        try {

            return ($this->dockerApiRequest(HttpMethodEnum::POST, '/containers/create?name=' . $config->Name, $container_config, allowed_codes: array(201)))->Id;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @param string $id_name
     * @return false|any
     */
    public function getContainerLogs(string $id_name, bool $raw = false): string|bool
    {

        try {

            $logs = $this->dockerApiRequest(HttpMethodEnum::GET, '/containers/' . $id_name . '/logs?stdout=true', allowed_codes: array(200));

            return $raw ? $logs : $this->formatContainerLogs($logs);

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @param string $id_name
     * @return false|any
     */
    public function getContainerErrorLogs(string $id_name, bool $raw = false): string|bool
    {

        try {

            $logs = $this->dockerApiRequest(HttpMethodEnum::GET, '/containers/' . $id_name . '/logs?stderr=true', allowed_codes: array(200));

            return $raw ? $logs : $this->formatContainerLogs($logs);

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @param string $id_name
     * @param bool $delete_volumes
     * @param bool $force
     * @param bool $delete_link
     * @return false|any
     */
    public function removeContainer(string $id_name, bool $delete_volumes = false, bool $force = false, bool $delete_link = false) {

        try {

            return $this->dockerApiRequest(HttpMethodEnum::DELETE, '/containers/' . $id_name . "?v=" . $delete_volumes . "&force=" . $force . "&link=" . $delete_link, allowed_codes: array(204));

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @param string $image
     * @param string $tag
     * @return false|any
     */
    public function pullImage(string $image, string $tag ) {

        try {

            return $this->dockerApiRequest(HttpMethodEnum::POST, "/images/create?fromImage=" . $image . "&tag=" . $tag, allowed_codes: array(200));

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @return array|false
     */
    public function listAllImages() {

        try {

            $images_response = $this->dockerApiRequest(HttpMethodEnum::GET, "/images/json", allowed_codes: array(200));
            $images = array();

            foreach ($images_response as $image) {

                $images[] = new DockerImage($image);

            }

            return $images;

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }

    /**
     * @param string $name
     * @param string $tag
     * @return false|DockerImage
     */
    public function getImage(string $name, string $tag ) {

        try {

            $container = $this->dockerApiRequest(HttpMethodEnum::GET, '/images/json?filters={"reference":["' . $name . ':' . $tag .'"]}', allowed_codes: array(200));

            if (empty($container)) return false;

            return new DockerImage((object)$container[0]);

        } catch (\ErrorException $e) {

            var_dump($e->getMessage());
            return false;

        }

    }


}
