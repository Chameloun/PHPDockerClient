<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\DockerConfig;

abstract class AbstractDockerContainerHostConfig
{
    /**
     * @param int|null $CpuShares
     * @param int|null $Memory
     * @param string|null $CgroupParent
     * @param int|null $BlkioWeight
     * @param array|null $BlkioWeightdevice
     * @param array|null $BlkioDeviceReadBps
     * @param array|null $BlkioDeviceWriteBps
     * @param array|null $BlkioDeviceReadIOps
     * @param array|null $BlkioDeviceWriteIOps
     * @param int|null $CpuPeriod
     * @param int|null $CpuQuota
     * @param int|null $CpuRealtimePeriod
     * @param int|null $CpuRealtimeRuntime
     * @param string|null $CpusetCpus
     * @param string|null $CpusetMems
     * @param array|null $Devices
     * @param array|null $DeviceRequests
     * @param array|null $DeviceCgroupRules
     * @param int|null $KernelMemory
     * @param int|null $KernelMemoryTCP
     * @param int|null $MemoryReservation
     * @param int|null $MemorySwap
     * @param int|null $MemorySwappiness
     * @param int|null $NanoCpus
     * @param bool $OomKillDisable
     * @param bool|null $Init
     * @param int|null $PidsLimit
     * @param array|null $Ulimits
     * @param array|null $Binds
     * @param string|null $ContainerIDFile
     * @param object|null $LogConfig
     * @param string|null $NetworkMode
     * @param object|null $PortBindings
     * @param object|null $RestartPolicy
     * @param bool $AutoRemove
     * @param string|null $VolumeDriver
     * @param string|null $VolumesFrom
     * @param array|null $Mounts
     * @param array|null $CapAdd
     * @param array|null $CapDrop
     * @param string|null $CgroupnsMode
     * @param array|null $Dns
     * @param array|null $DnsOptions
     * @param array|null $DnsSearch
     * @param array|null $ExtraHosts
     * @param array|null $GroupAdd
     * @param string|null $IpcMode
     * @param string|null $Cgroup
     * @param array|null $Links
     * @param int|null $OomScoreAdj
     * @param string|null $PidMode
     * @param bool|null $Privileged
     * @param bool|null $PublishAllPorts
     * @param bool|null $ReadonlyRootfs
     * @param array|null $SecurityOpt
     * @param object|null $StorageOpt
     * @param object|null $Tmpfs
     * @param string|null $UTSMode
     * @param string|null $UsernsMode
     * @param int|null $ShmSize
     * @param object|null $Sysctls
     * @param string|null $Runtime
     * @param array|null $MaskedPaths
     * @param array|null $ReadonlyPaths
     */
    public function __construct(
        public ?int $CpuShares = null,
        public ?int $Memory = null,
        public ?string $CgroupParent = null,
        public ?int $BlkioWeight = null,
        public ?array $BlkioWeightdevice = null,
        public ?array $BlkioDeviceReadBps = null,
        public ?array $BlkioDeviceWriteBps = null,
        public ?array $BlkioDeviceReadIOps = null,
        public ?array $BlkioDeviceWriteIOps = null,
        public ?int $CpuPeriod = null,
        public ?int $CpuQuota = null,
        public ?int $CpuRealtimePeriod = null,
        public ?int $CpuRealtimeRuntime = null,
        public ?string $CpusetCpus = null,
        public ?string $CpusetMems = null,
        public ?array $Devices = null,
        public ?array $DeviceRequests = null,
        public ?array $DeviceCgroupRules = null,
        public ?int $KernelMemory = null,
        public ?int $KernelMemoryTCP = null,
        public ?int $MemoryReservation = null,
        public ?int $MemorySwap = null,
        public ?int $MemorySwappiness = null,
        public ?int $NanoCpus = null,
        public bool $OomKillDisable = false,
        public ?bool $Init = null,
        public ?int $PidsLimit = null,
        public ?array $Ulimits = null,
        public ?array $Binds = null,
        public ?string $ContainerIDFile = null,
        public ?object $LogConfig = null,
        public ?string $NetworkMode = null,
        public ?object $PortBindings = null,
        public ?object $RestartPolicy = null,
        public bool $AutoRemove = false,
        public ?string $VolumeDriver = null,
        public ?string $VolumesFrom = null,
        public ?array $Mounts = null,
        public ?array $CapAdd = null,
        public ?array $CapDrop = null,
        public ?string $CgroupnsMode = null,
        public ?array $Dns = null,
        public ?array $DnsOptions = null,
        public ?array $DnsSearch = null,
        public ?array $ExtraHosts = null,
        public ?array $GroupAdd = null,
        public ?string $IpcMode = null,
        public ?string $Cgroup = null,
        public ?array $Links = null,
        public ?int $OomScoreAdj = null,
        public ?string $PidMode = null,
        public ?bool $Privileged = null,
        public ?bool $PublishAllPorts = null,
        public ?bool $ReadonlyRootfs = null,
        public ?array $SecurityOpt = null,
        public ?object $StorageOpt = null,
        public ?object $Tmpfs = null,
        public ?string $UTSMode = null,
        public ?string $UsernsMode = null,
        public ?int $ShmSize = null,
        public ?object $Sysctls = null,
        public ?string $Runtime = null,
        public ?array $MaskedPaths = null,
        public ?array $ReadonlyPaths = null
    ) {}

    /**
     * @param string $host
     * @param string $container
     * @param array|null $options
     * @return void
     */
    abstract public function setBinds(string $host, string $container, ?array $options = null): void;
}
