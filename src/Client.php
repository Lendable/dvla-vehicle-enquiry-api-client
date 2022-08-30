<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\VehiclesScope;
use Psr\Http\Message\UriInterface;

class Client
{
    public function __construct(private HttpClient $httpClient, private UriInterface $uri)
    {
    }

    public function vehicles(): VehiclesScope
    {
        return new VehiclesScope($this->httpClient, $this->uri);
    }
}
