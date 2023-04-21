<?php

namespace LocuTeam\PHPDockerClient;

final class DockerContainer {

    /**
     * @var string
     */
    private string $id;

    /**
     * @var string|mixed
     */
    private string $name;

    /**
     * @param \stdClass $container_info
     */
    public function __construct(\stdClass $container_info)
    {
        $this->id = $container_info->Id;

        if (isset($container_info->Names[0]))
            $this->name = $container_info->Names[0];
        else 
            $this->name = $container_info->Name;

    }

    /**
     * @return string
     */
    public function getId() : string {

        return $this->id;

    }

    /**
     * @return string
     */
    public function getName() : string {

        return $this->name;

    }

}

?>