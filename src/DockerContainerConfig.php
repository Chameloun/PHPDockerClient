<?php

namespace LocuTeam\PHPDockerClient;

use LocuTeam\PHPDockerClient\DockerConfig\AbstractDockerContainerConfig;

class DockerContainerConfig extends AbstractDockerContainerConfig {

    public string $Name;

    public function createRequestBody()
    {
        return json_encode($this);
    }


}

?>