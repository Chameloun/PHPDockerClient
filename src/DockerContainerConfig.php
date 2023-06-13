<?php

namespace LocuTeam\PHPDockerClient;

use LocuTeam\PHPDockerClient\DockerConfig\AbstractDockerContainerConfig;

/**
 *
 */
class DockerContainerConfig extends AbstractDockerContainerConfig {

    /**
     * @var string
     */
    public string $Name;

    /**
     * @throws \JsonException
     */
    public function createRequestBody(): bool|string
    {

        return json_encode($this, JSON_THROW_ON_ERROR);

    }

    /**
     * @return string
     */
    public function getName() : string
    {

            return $this->Name;

    }


}

?>