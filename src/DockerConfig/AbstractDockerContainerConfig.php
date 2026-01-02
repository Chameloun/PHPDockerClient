<?php

namespace Chameloun\PHPDockerClient\DockerConfig;

use Chameloun\PHPDockerClient\DockerContainerHostConfig;

abstract class AbstractDockerContainerConfig {

    /**
     * @param string $Name
     * @param string $Image
     * @param string|null $Platform
     * @param string|null $Hostname
     * @param string|null $Domainname
     * @param string|null $User
     * @param bool|null $AttachStdin
     * @param bool|null $AttachStdout
     * @param bool|null $AttachStderr
     * @param array|null $ExposedPorts
     * @param bool|null $Tty
     * @param bool|null $OpenStdin
     * @param bool|null $StdinOnce
     * @param array|null $Env
     * @param array|null $Cmd
     * @param array|null $ArgsEscaped
     * @param object|null $Volumes
     * @param string|null $WorkingDir
     * @param array|null $Entrypoint
     * @param bool|null $NetworkDisabled
     * @param string|null $MacAddress
     * @param array|null $OnBuild
     * @param object|null $Labels
     * @param string|null $StopSignal
     * @param int|null $StopTimeout
     * @param array|null $Shell
     * @param DockerContainerHostConfig|null $HostConfig
     * @throws \ErrorException
     */
    public function __construct(

        public string $Name,
        public string $Image,
        public ?string $Platform = null,
        public ?string $Hostname = null,
        public ?string $Domainname = null,
        public ?string $User = null,
        public ?bool $AttachStdin = false,
        public ?bool $AttachStdout = true,
        public ?bool $AttachStderr = true,
        public ?array $ExposedPorts = null,
        public ?bool $Tty = false,
        public ?bool $OpenStdin = false,
        public ?bool $StdinOnce = false,
        public ?array $Env = null,
        public ?array $Cmd = null,
        public ?array $ArgsEscaped = null,
        public ?object $Volumes = null,
        public ?string $WorkingDir = null,
        public ?array $Entrypoint = null,
        public ?bool $NetworkDisabled = false,
        public ?string $MacAddress = null,
        public ?array $OnBuild = null,
        public ?object $Labels = null,
        public ?string $StopSignal = null,
        public ?int $StopTimeout = null,
        public ?array $Shell = null,
        public ?DockerContainerHostConfig $HostConfig = null


    ){}

    /**
     * @return mixed
     */
    abstract public function createRequestBody();


}

?>