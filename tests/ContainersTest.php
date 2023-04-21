<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LocuTeam\PHPDockerClient\DockerClient;

final class ContainersTest extends TestCase
{
    public $client;

    protected function setUp(): void
    {
        $this->client = new DockerClient();
    }

    public function testListContainers(): void
    {
        $containers = $this->client->listContainers();
        $this->assertIsArray($containers);
    }

    public function testStopAllContainers(): void
    {
        $container = $this->client->stopAllContainers();
        $this->assertTrue($container);
    }

    public function testRestartAllContainers(): void
    {
        $container = $this->client->restartAllContainers();
        $this->assertTrue($container);
    }

    public function testStartAllContainers(): void
    {
        $this->markTestSkipped('must be revisited.');
        
        $container = $this->client->startAllContainers();
        $this->assertTrue($container);
    }


}