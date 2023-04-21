<?php

namespace LocuTeam\PHPDockerClient;

use LocuTeam\PHPDockerClient\DockerConfig\AbstractDockerContainerHostConfig;

class DockerContainerHostConfig extends AbstractDockerContainerHostConfig
{

    /**
     * @param string $host
     * @param string $container
     * @param array|null $options
     * @return void
     */
    public function setBinds(string $host, string $container, ?array $options = null): void
    {
        if ($this->Binds === null) {
            $this->Binds = [];
        }

        $bind = $host . ":" . $container;

        if ($options !== null) {

            $bind .= ":";

            foreach ($options as $option) {

                $bind .= $option . ",";

            }

        }

        $this->Binds[] = rtrim($bind, ",");

    }

}