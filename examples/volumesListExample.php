<?php

namespace Chameloun\PHPDockerClient\Examples;

use Chameloun\PHPDockerClient\DockerClient;

include __DIR__  .  '/../vendor/autoload.php' ;

$client = new DockerClient();

//$client->setUnixSocket("/Users/matejsoukup/.docker/run/docker.sock");

print_r($client->listVolumes());
