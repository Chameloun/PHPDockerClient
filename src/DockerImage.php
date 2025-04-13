<?php

namespace Chameloun\PHPDockerClient;

use stdClass;

/**
 *
 */
class DockerImage
{

    /**
     * @var ?string
     */
    private ?string $Id;

    /**
     * @var ?string
     */
    private ?string $ParentId;

    /**
     * @var ?array
     */
    private ?array $RepoTags;

    /**
     * @var ?array
     */
    private ?array $RepoDigests;

    /**
     * @var ?string
     */
    private ?string $Created;

    /**
     * @var ?int
     */
    private ?int $Size;

    /**
     * @var ?int
     */
    private ?int $SharedSize;

    /**
     * @var ?stdClass
     */
    private ?stdClass $Labels;

    /**
     * @var ?int
     */
    private ?int $Containers;

    /**
     * @param stdClass $image_info
     */
    public function __construct(stdClass $image_info) {

        $this->Id = $image_info->Id;

        $this->RepoTags = $image_info->RepoTags;

        $this->ParentId = $image_info->ParentId;

        $this->RepoDigests = $image_info->RepoDigests;

        $this->Created = $image_info->Created;

        $this->Size = $image_info->Size;

        $this->SharedSize = $image_info->SharedSize;

        //$this->VirtualSize = $image_info->VirtualSize;

        $this->Labels = $image_info->Labels;

        $this->Containers = $image_info->Containers;

    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->Id;
    }

    /**
     * @return string
     */
    public function getParentId(): string
    {
        return $this->ParentId;
    }

    /**
     * @return array
     */
    public function getRepoTags(): array
    {
        return $this->RepoTags;
    }

    /**
     * @return array
     */
    public function getRepoDigests(): array
    {
        return $this->RepoDigests;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->Created;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->Size;
    }

    /**
     * @return int
     */
    public function getSharedSize(): int
    {
        return $this->SharedSize;
    }

    /**
     * @return int
     */
    public function getVirtualSize(): int
    {
        return $this->VirtualSize;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->Labels;
    }

    /**
     * @return int
     */
    public function getContainers(): int
    {
        return $this->Containers;
    }



}