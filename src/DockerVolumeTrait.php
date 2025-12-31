<?php

namespace Chameloun\PHPDockerClient;

use Chameloun\PHPDockerClient\DockerConfig\HttpMethodEnum;
use Chameloun\PHPDockerClient\DockerError\DockerErrorCodeEnum;
use Chameloun\PHPDockerClient\DockerError\DockerException;

trait DockerVolumeTrait
{

    public function listVolumes() {

        try {

            $volumes_response = $this->dockerApiRequest(HttpMethodEnum::GET, '/volumes', allowed_codes: array(200));
            $volumes = array();

            foreach ($volumes_response->Volumes as $volume) {

                $volumes[] = new DockerVolume($volume);

            }

            return $volumes;

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }

}