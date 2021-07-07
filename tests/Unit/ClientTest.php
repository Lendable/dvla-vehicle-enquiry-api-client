<?php

declare(strict_types=1);

namespace Tests\Unit\Lendable\Dvla\VehicleEnquiry;

use Lendable\Dvla\VehicleEnquiry\Client;
use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\VehiclesScope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class ClientTest extends TestCase
{
    /**
     * @var HttpClient&MockObject
     */
    private HttpClient $httpClient;

    /**
     * @var UriInterface&MockObject
     */
    private UriInterface $baseUri;

    private Client $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClient::class);
        $this->baseUri = $this->createMock(UriInterface::class);

        $this->fixture = new Client(
            $this->httpClient,
            $this->baseUri
        );
    }

    /**
     * @test
     */
    public function it_should_create_the_vehicles_scope_with_its_own_path(): void
    {
        $this->baseUri->method('getPath')
            ->willReturn('/aaa/bbb/ccc');

        $modifiedPath = $this->createMock(UriInterface::class);

        $this->baseUri->method('withPath')
            ->with('/aaa/bbb/ccc/vehicles')
            ->willReturn($modifiedPath);

        $vehiclesScope = $this->fixture->vehicles();

        $this->assertInstanceOf(VehiclesScope::class, $vehiclesScope);
    }
}
