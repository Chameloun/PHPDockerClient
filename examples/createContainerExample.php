<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\DockerClient;
use LocuTeam\PHPDockerClient\DockerContainerConfig;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new DockerClient();

$config = new DockerContainerConfig("gcc", "gcc:latest");

$client->createContainer($config);

$client->startContainer("gcc");

$client->waitForContainer("gcc");

var_dump($client->getContainer("gcc")->getRunDuration());

$client->removeContainer("gcc");

?>