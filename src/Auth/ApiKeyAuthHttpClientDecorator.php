<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Auth;

use Lendable\Dvla\VehicleEnquiry\Auth\ValueObject\ApiKey;
use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Client\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Client\Response;
use Psr\Http\Message\UriInterface;

class ApiKeyAuthHttpClientDecorator implements HttpClient
{
    private HttpClient $innerClient;

    private ApiKey $apiKey;

    public function __construct(
        HttpClient $innerClient,
        ApiKey $apiKey
    ) {
        $this->innerClient = $innerClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @param array<string|int, mixed>|null $data
     * @param array<string, string|array<string>> $headers
     */
    public function request(UriInterface $uri, HttpMethod $method, ?array $data = null, array $headers = []): Response
    {
        return $this->innerClient->request(
            $uri,
            $method,
            $data,
            \array_merge(
                [
                    'x-api-key' => $this->apiKey->toString(),
                ],
                $headers
            )
        );
    }
}
