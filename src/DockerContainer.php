<?php

namespace Chameloun\PHPDockerClient;

final class DockerContainer {

    /**
     * @var string
     */
    public string $id;

    /**
     * @var string|mixed
     */
    public string $name;

    /**
     * @var \stdClass
     */
    public \stdClass|string $state;

    /**
     * @param \stdClass $container_info
     */
    public function __construct(\stdClass $container_info)
    {
        $this->id = $container_info->Id;

        $this->state = $container_info->State;

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

    /**
     * @return bool
     */
    public function isRunning(): bool {

        return $this->state->Running;

    }

    /**
     * @return string
     */
    public function getStatus(): string {

        return $this->state->Status;

    }

    /**
     * @return bool
     */
    public function isPaused(): bool {

        return $this->state->Paused;

    }

    /**
     * @return bool
     */
    public function isDead(): bool {

        return $this->state->Dead;

    }

    /**
     * @return int
     */
    public function getExitCode(): int {

        return $this->state->ExitCode;

    }

    /**
     * @return string
     */
    public function getError(): string {

        return $this->state->Error;

    }

    /**
     * @return \DateTime
     */
    public function getStartedAt(): \DateTime {

        return new \DateTime($this->state->StartedAt);

    }

    /**
     * @return \DateTime
     */
    public function getFinishedTime(): \DateTime {

        return new \DateTime($this->state->FinishedAt);

    }

    /**
     * @return \DateInterval
     */
    public function getRunDuration(): \DateInterval {

        return date_diff($this->getStartedAt(), $this->getFinishedTime());

    }

}

?>