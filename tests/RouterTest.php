<?php declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class RouterTest extends TestCase
{
    private Process $process;

    public function setUp(): void
    {
        $path = getcwd() . "/server";
        $this->process = new Process(["php", "-S", "localhost:8080", "-t", $path]);
        $this->process->start();

        usleep(100000); //wait for server to get going
        if (!$this->process->isRunning()) {
            $this->fail("Process is not running");
        }
    }

    /**
     * @throws GuzzleException
     */
    public function test404(): void
    {
        $client = new Client(['http_errors' => false]);

        $response = $client->request("GET", "http://localhost:8080/test");
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function test200(): void
    {
        $client = new Client(['http_errors' => false]);

        $response = $client->request("GET", "http://localhost:8080");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function tearDown(): void
    {
        $this->process->stop();
    }
}
