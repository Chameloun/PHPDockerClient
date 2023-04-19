<?php

namespace LocuTeam\PHPDockerClient;

use LocuTeam\PHPDockerClient\DockerConfig\AbstractDockerContainerConfig;

class DockerContainerConfig extends AbstractDockerContainerConfig {


    public function __construct(...$config)
    {
        parent::__construct(...$config);
    }

    public function createRequestBody()
    {
        return json_encode($this);
    }


}

?>