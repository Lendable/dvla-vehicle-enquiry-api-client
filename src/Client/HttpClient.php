<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\HttpMethod;
use Psr\Http\Message\UriInterface;

interface HttpClient
{
    /**
     * @param array<string|int, mixed>|null $data
     * @param array<string, string|array<string>> $headers
     */
    public function request(UriInterface $uri, HttpMethod $method, ?array $data = null, array $headers = []): Response;
}
