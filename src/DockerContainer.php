<?php

namespace LocuTeam\PHPDockerClient;

final class DockerContainer {

    private string $id;

    private string $name;

    public function __construct(\stdClass $container_info)
    {
        $this->id = $container_info->Id;

        if (isset($container_info->Names[0]))
            $this->name = $container_info->Names[0];
        else 
            $this->name = $container_info->Name;

    }

    public function getId() : string {

        return $this->id;

    }

    public function getName() : string {

        return $this->name;

    }

}

?>