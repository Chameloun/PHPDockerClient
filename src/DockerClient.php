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

        if ($method === HttpMethodEnum::PUT and $data !== "") {

            $curl_config[CURLOPT_POSTFIELDS] = $data;
            $curl_config[CURLOPT_HTTPHEADER] = array('Accept: application/json', 'Content-Type: application/x-tar');

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

        //throw new DockerException("Request to Docker API failed!", DockerErrorCodeEnum::ERROR);
        throw new DockerException(curl_error($curl), DockerErrorCodeEnum::ERROR);

    }

    use DockerContainerTrait;

    use DockerExecTrait;

    use DockerImageTrait;

    use DockerVolumeTrait;

}
