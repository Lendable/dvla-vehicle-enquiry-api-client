<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Lendable\Dvla\VehicleEnquiry\Client\Content;
use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Client\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Client\Response;
use Psr\Http\Message\UriInterface;

class GuzzleClientDecorator implements HttpClient
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function request(UriInterface $uri, HttpMethod $method, ?array $data = null, array $headers = []): Response
    {
        $response = $this->client->request(
            $method->toString(),
            $uri,
            [
                RequestOptions::HEADERS => $headers,
                RequestOptions::JSON => $data,
            ]
        );

        return Response::with(
            $response->getStatusCode(),
            $response->getHeaders(),
            Content::fromString($response->getBody()->getContents())
        );
    }
}
