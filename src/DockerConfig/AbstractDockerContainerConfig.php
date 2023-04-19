<?php

namespace LocuTeam\PHPDockerClient\DockerConfig;

use stdClass;

abstract class AbstractDockerContainerConfig {

    public function __construct(
        
        public string $name,

        public string $Image,

        public string $platform = "",
    
        public string $Hostname = "",
    
        public string $Domainname = "",
    
        public string $User = "",
    
        public bool $AttachStdin = false,
    
        public bool $AttachStdout = true,
    
        public bool $AttachStderr = true,
    
        public $ExposedPorts = null,
    
        public bool $Tty = false,
    
        public bool $OpenStdin = false,
    
        public bool $StdinOnce = false,
    
        public array $Env = array(),
    
        public array $Cmd = array("bash"),
    
        public $ArgsEscaped = null,
    
        public object $Volumes = new stdClass(),
    
        public string $WorkingDir = "",
    
        public array $Entrypoint = array(""),
    
        public bool $NetworkDisabled = false,
    
        public $MacAddress = null,
    
        public $OnBuild = null,
    
        public object $Labels = new stdClass(),
    
        public $StopSignal = "SIGTERM",
    
        public $StopTimeout = 10,
    
        public $Shell = null,

        public $HostConfig = new stdClass()


    ){

        if (!preg_match("/\/[a-zA-Z0-9][a-zA-Z0-9_.-]+$/i", $this->name)) {
            $this->name = "/" . $this->name;
        }

        if (!preg_match("/\/[a-zA-Z0-9][a-zA-Z0-9_.-]+$/i", $this->name)) {
            
            throw new \ErrorException("Container name is wrong!", 1, 1);

        }

    }

    abstract public function createRequestBody();


}

?>