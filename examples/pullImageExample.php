<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\DockerClient;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new DockerClient();

$client->pullImage("gcc","latest");

var_dump($client->getImage("gcc", "latest")->getRepoTags());
