<?php

namespace LocuTeam\PHPDockerClient\DockerError;
class DockerException extends \Exception {
    public function __construct(string $message = "", DockerErrorCodeEnum $code = DockerErrorCodeEnum::SUCCESS, \Throwable $previous = null) {

        parent::__construct($message, $code->value, $previous);

    }

}