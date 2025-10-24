<?php

namespace Chameloun\PHPDockerClient;

use stdClass;

/**
 *
 */
class DockerVolume
{

    /**
     * @var string|null
     */
    private ?string $Name;
    /**
     * @var string|null
     */
    private ?string $Driver;
    /**
     * @var string|null
     */
    private ?string $Mountpoint;
    /**
     * @var string|null
     */
    private ?string $CreatedAt;
    /**
     * @var array|null
     */
    private ?array $Status;
    /**
     * @var StdClass|null
     */
    private ?StdClass $Labels;
    /**
     * @var string|null
     */
    private ?string $Scope;
    /**
     * @var array|null
     */
    private ?array $ClusterVolume;
    /**
     * @var array|null
     */
    private ?array $Options;
    /**
     * @var array|null
     */
    private ?array $UsageData;

    /**
     * @param stdClass $image_info
     */
    public function __construct(stdClass $image_info)
    {
        $this->Name = $image_info->Name;
        $this->Driver = $image_info->Driver;
        $this->Mountpoint = $image_info->Mountpoint;
        $this->CreatedAt = $image_info->CreatedAt;
        @$this->Status = $image_info->Status;
        $this->Labels = $image_info->Labels;
        $this->Scope = $image_info->Scope;
        @$this->ClusterVolume = $image_info->ClusterVolume;
        @$this->Options = $image_info->Options;
        @$this->UsageData = $image_info->UsageData;
    }


}