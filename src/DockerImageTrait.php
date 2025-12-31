<?php

namespace Chameloun\PHPDockerClient;

use Chameloun\PHPDockerClient\DockerConfig\HttpMethodEnum;
use Chameloun\PHPDockerClient\DockerError\DockerErrorCodeEnum;
use Chameloun\PHPDockerClient\DockerError\DockerException;

trait DockerImageTrait
{

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

}