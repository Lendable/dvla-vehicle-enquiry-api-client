<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Client\Response;
use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\Content;
use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Error\RequestFailed;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithError;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithMessage;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Error;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Message;
use Nyholm\Psr7\Request;
use PHPUnit\Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\UriInterface;

class Psr18ClientDecorator implements HttpClient
{
    private const HEADERS = [
        'Content-Type' => 'application/json; charset=utf-8',
    ];

    private ClientInterface $client;

    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    public function request(UriInterface $uri, HttpMethod $method, ?array $data = null, array $headers = []): Response
    {
        $request = $this->createPsrRequest($method, $uri, $data, $headers);

        try {
            $psrResponse = $this->client->sendRequest($request);
            $statusCode = $psrResponse->getStatusCode();
            $content = (string) $psrResponse->getBody();

            $this->handleInvalidStatusCode($statusCode, $content);

            return Response::with(
                $statusCode,
                $psrResponse->getHeaders(),
                Content::fromString($content)
            );
        } catch (DvlaVehicleEnquiryFailure | Exception $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw RequestFailed::dueTo($exception);
        }
    }

    private function createPsrRequest(HttpMethod $method, UriInterface $uri, ?array $data, array $headers): Request
    {
        $requestBody = $data !== null ? \json_encode($data, JSON_THROW_ON_ERROR) : null;

        return new Request(
            $method->toString(),
            $uri,
            \array_merge(self::HEADERS, $headers),
            $requestBody
        );
    }

    private function handleInvalidStatusCode(int $statusCode, string $content): void
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        try {
            $responseData = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (isset($responseData['errors'][0])) {
                throw RequestRejectedWithError::of(Error::fromArray($responseData['errors'][0]));
            }

            if (isset($responseData['message'])) {
                throw RequestRejectedWithMessage::of(Message::fromString($responseData['message']));
            }
        } catch (\JsonException $exception) {
            throw RequestFailed::dueToInvalidJson($statusCode, $exception);
        }

        throw RequestFailed::dueToInvalidStatusCode($statusCode);
    }
}
