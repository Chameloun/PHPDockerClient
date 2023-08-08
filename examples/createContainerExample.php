<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\DockerClient;
use LocuTeam\PHPDockerClient\DockerContainerConfig;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new DockerClient();

$config1 = new DockerContainerConfig("gcc1", "gcc:latest");
$config2 = new DockerContainerConfig("gcc2", "gcc:latest");

$client->createContainer($config1);
$client->createContainer($config2);

$client->setWorkingContainer("gcc1");
$client->removeContainer();

$client->setWorkingContainer("gcc2");
$client->removeContainer();

?>