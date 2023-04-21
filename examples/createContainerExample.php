<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\DockerClient;
use LocuTeam\PHPDockerClient\DockerContainerConfig;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new DockerClient();

$config = new DockerContainerConfig("ubuntuExample", "ubuntu", Tty: true);

$client->createContainer($config);

$client->startContainer("ubuntuExample");

var_dump($client->getContainer("ubuntuExample"));

?>