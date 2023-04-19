<?php
declare(strict_types=1);

namespace LocuTeam\PHPDockerClient\Examples;

use LocuTeam\PHPDockerClient\PHPDockerClient;

include __DIR__  .  '/../vendor/autoload.php' ;


$client = new PHPDockerClient();

$containers = $client->listContainersIds();

var_dump($containers);

?>