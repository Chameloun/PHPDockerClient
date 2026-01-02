<?php

namespace Chameloun\PHPDockerClient;

use Chameloun\PHPDockerClient\DockerConfig\HttpMethodEnum;
use Chameloun\PHPDockerClient\DockerError\DockerErrorCodeEnum;
use Chameloun\PHPDockerClient\DockerError\DockerException;

trait DockerContainerTrait {

    /**
     * @param string $logs
     * @return string
     */
    private function formatContainerLogs(string $logs): string {

        $i = 0;
        $output = '';
        $len = strlen($logs);

        while ($i < $len) {

            if ($i + 8 > $len) {
                break;
            }

            $header = substr($logs, $i, 8);
            $size = unpack('N', substr($header, 4, 4))[1];

            $i += 8;

            if ($i + $size <= $len) {
                $output .= substr($logs, $i, $size);
            }

            $i += $size;
        }

        return trim($output);
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
    public function containerArchive(string $data) {

        try {

            return ($this->dockerApiRequest(HttpMethodEnum::PUT, "/containers/" . ($id_name ?? $this->getWorkingContainer()) . "/archive?path=/tmp", data: $data, allowed_codes: array(200)));

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }

}