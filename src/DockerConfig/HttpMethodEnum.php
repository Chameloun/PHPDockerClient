<?php

namespace Chameloun\PHPDockerClient\DockerConfig;

/**
 *
 * @enum HTTP_METHOD
 *
 */
enum HttpMethodEnum : string {

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';

}