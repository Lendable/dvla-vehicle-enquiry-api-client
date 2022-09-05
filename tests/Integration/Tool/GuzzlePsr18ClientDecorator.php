<?php

declare(strict_types=1);

namespace Tests\Integration\Lendable\Dvla\VehicleEnquiry\Tool;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\RequestOptions;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzlePsr18ClientDecorator implements ClientInterface
{
    public function __construct(private GuzzleClientInterface $client)
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->client->request(
            $request->getMethod(),
            $request->getUri(),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => $request->getHeaders(),
                RequestOptions::BODY => $request->getBody(),
            ]
        );

        return new Response(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()
        );
    }
}
