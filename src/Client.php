<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\VehiclesScope;
use Psr\Http\Message\UriInterface;

class Client
{
    private HttpClient $httpClient;

    private UriInterface $uri;

    public function __construct(HttpClient $httpClient, UriInterface $uri)
    {
        $this->httpClient = $httpClient;
        $this->uri = $uri;
    }

    public function vehicles(): VehiclesScope
    {
        return new VehiclesScope($this->httpClient, $this->uri);
    }
}
