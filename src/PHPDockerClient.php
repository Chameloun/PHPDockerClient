<?php

namespace LocuTeam\PHPDockerClient;


/**
 * 
 * @enum HTTP_METHOD
 * 
 */
enum HTTP_METHOD : string {

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';

}

/**
 * 
 * @class PHPDocker
 * 
 */

final class PHPDockerClient {

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
    public function __construct( bool $use_socket, string $hostname = "localhost", int $port = 2375)
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
     * @param HTTP_METHOD $method
     * @param string $path
     * @param array $data
     * @param array $allowed_codes
     * 
     * @return array
     * 
     */
    private function dockerApiRequest( HTTP_METHOD $method, string $path, array $data = array(), array $allowed_codes = array(200, 201, 202, 204)) {

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

        curl_setopt_array($curl, $curl_config);
        
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        if (!curl_errno($curl)) {

            if (!in_array($info['http_code'], $allowed_codes)) {

                throw new \ErrorException("Request to Docker API failed! HTTP Code: " . $info['http_code'], 1, 1);

            }

            return json_decode($response);

        } else {

            throw new \ErrorException("Request to Docker API failed!", 1, 1);

        }
        
        curl_close($curl);

    }

    /**
     * 
     * @param bool $running
     * 
     * @return array
     * 
     */
    public function listContainers(bool $running = false) : array {


        return $this->dockerApiRequest(HTTP_METHOD::GET, "/containers/json?all=" . $running, allowed_codes: array(200));


    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    private function waitForContainer( string $id ) : bool {

        try {

            $this->dockerApiRequest(HTTP_METHOD::POST, "/containers/" . $id . "/wait", allowed_codes: array(200));
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
    public function listContainersIds(bool $running = false) : array {

        $containers = $this->listContainers($running);
        $ids = array();

        foreach ($containers as $container) {

            array_push($ids, $container->Id);

        }

        return $ids;

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

            $this->dockerApiRequest(HTTP_METHOD::POST, "/containers/" . $id . "/stop", allowed_codes: array(204, 304));
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

        $ids = $this->listContainersIds();

        foreach ($ids as $id) {

            $output = $this->stopContainer($id);
            $this->waitForContainer($id);

            if (!$output) return false;

        }

        return true;

    }

    /**
     * 
     * @param string $id
     * 
     * @return bool
     * 
     */
    public function startContainer( string $id ) : bool {

        try {

            $this->dockerApiRequest(HTTP_METHOD::POST, "/containers/" . $id . "/start", allowed_codes: array(204, 304));
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

        $ids = $this->listContainersIds(true);

        foreach ($ids as $id) {

            $output = $this->startContainer($id);

            if (!$output) return false;

        }

        return true;

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

            $this->dockerApiRequest(HTTP_METHOD::POST, "/containers/" . $id . "/restart", allowed_codes: array(204));
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
    public function restartAllContainers() : bool {

        $ids = $this->listContainersIds();

        foreach ($ids as $id) {

            $output = $this->restartContainer($id);

            if (!$output) return false;

        }

        return true;

    }


}
