<?php

namespace LocuTeam\PHPDockerClient\DockerError;

/**
 *
 * @enum DOCKER_ERROR_CODES
 *
 */

enum DockerErrorCodeEnum : int {

    case ERROR = 0;

    case WARNING = 1;

    case SUCCESS = 2;

}