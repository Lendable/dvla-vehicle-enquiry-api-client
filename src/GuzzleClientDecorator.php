<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lendable\Dvla\VehicleEnquiry\Client\Content;
use Lendable\Dvla\VehicleEnquiry\Client\HttpClient;
use Lendable\Dvla\VehicleEnquiry\Client\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Client\Response;
use Lendable\Dvla\VehicleEnquiry\Error\RequestFailed;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithError;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithMessage;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Error;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Message;
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
        try {
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
        } catch (BadResponseException $exception) {
            // 4xx and 5xx responses
            throw $this->decodeError($exception);
        } catch (GuzzleException $exception) {
            throw RequestFailed::dueTo($exception);
        }
    }

    private function decodeError(BadResponseException $clientException): DvlaVehicleEnquiryFailure
    {
        $statusCode = $clientException->getResponse()->getStatusCode();
        $responseBody = $clientException->getResponse()->getBody()->getContents();

        try {
            $responseData = \json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $jsonException) {
            return RequestFailed::dueToInvalidJson($statusCode, $jsonException);
        }

        if (isset($responseData['errors'][0])) {
            return RequestRejectedWithError::of(Error::fromArray($responseData['errors'][0]), $clientException);
        }

        if (isset($responseData['message'])) {
            return RequestRejectedWithMessage::of(Message::fromString($responseData['message']), $clientException);
        }

        return RequestFailed::dueToInvalidStatusCode($statusCode);
    }
}
