<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\PHPDockerClient;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new PHPDockerClient();

$containers = $client->getContainer("/locuboard-docker-locuboard-web-1");

var_dump($containers);

?>