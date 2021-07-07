<?php

declare(strict_types=1);

namespace Tests\Unit\Lendable\Dvla\VehicleEnquiry;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Lendable\Dvla\VehicleEnquiry\Client\Content;
use Lendable\Dvla\VehicleEnquiry\Client\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Client\Response;
use Lendable\Dvla\VehicleEnquiry\Error\RequestFailed;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithError;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithMessage;
use Lendable\Dvla\VehicleEnquiry\GuzzleClientDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class GuzzleClientDecoratorTest extends TestCase
{
    /**
     * @var ClientInterface&MockObject
     */
    private ClientInterface $httpClient;

    private GuzzleClientDecorator $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);

        $this->fixture = new GuzzleClientDecorator(
            $this->httpClient
        );
    }

    /**
     * @test
     */
    public function it_should_make_http_call_via_the_guzzle_client(): void
    {
        $uri = new Uri('https://127.0.0.1:1234/aa/bb/cc?dd=ee&ff=11');
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $uri,
                [
                    RequestOptions::HEADERS => [
                        'x-special' => 'data-in-the-header',
                    ],
                    RequestOptions::JSON => [
                        'foo' => 1,
                        'baz' => true,
                    ],
                ]
            )
            ->willReturn(
                new GuzzleResponse(
                    200,
                    [
                        'Response-Test-Header1' => 'asd-1234-XYZ',
                        'Response-Test-Header2' => [
                            '2.1',
                            '2.2',
                        ],
                    ],
                    '{"test":1}'
                )
            );

        $response = $this->fixture->request(
            $uri,
            HttpMethod::post(),
            [
                'foo' => 1,
                'baz' => true,
            ],
            [
                'x-special' => 'data-in-the-header',
            ]
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->statusCode());
        $this->assertInstanceOf(Content::class, $response->content());
        $this->assertSame('{"test":1}', $response->content()->toString());
        $this->assertSame(['test' => 1], $response->content()->decode());
        $this->assertSame(
            [
                'Response-Test-Header1' => [
                    'asd-1234-XYZ',
                ],
                'Response-Test-Header2' => [
                    '2.1',
                    '2.2',
                ],
            ],
            $response->headers()
        );
    }

    /**
     * @psalm-param class-string<\Throwable> $expectedExceptionClass
     *
     * @test
     * @dataProvider providesErrorResponses
     */
    public function it_should_throw_exception_on_api_error(
        string $expectedExceptionClass,
        string $expectedExceptionMessage,
        int $statusCode,
        string $responseBody
    ): void {
        $httpClientException = new ClientException(
            'Test exception message',
            $this->createMock(Request::class),
            new GuzzleResponse($statusCode, [], $responseBody),
        );
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException($httpClientException);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fixture->request(
            $this->createMock(UriInterface::class),
            HttpMethod::post()
        );
    }

    public function providesErrorResponses(): iterable
    {
        yield 'Vehicle Not Found error' => [
            'expectedExceptionClass' => RequestRejectedWithError::class,
            'expectedExceptionMessage' => 'Request rejected by DVLA Vehicle Enquiry with status 404, code 404, title "Vehicle Not Found" and message "Record for vehicle not found".',
            'statusCode' => 404,
            'responseBody' => '{
                "errors": [
                    {
                        "status": "404",
                        "code": "404",
                        "title": "Vehicle Not Found",
                        "detail": "Record for vehicle not found"
                    }
                ]
            }',
        ];

        yield 'Bad Request error' => [
            'expectedExceptionClass' => RequestRejectedWithError::class,
            'expectedExceptionMessage' => 'Request rejected by DVLA Vehicle Enquiry with status 400, code ENQ103, title "Bad Request" and message "Invalid format for field - vehicle registration number".',
            'statusCode' => 400,
            'responseBody' => '{
                "errors": [
                    {
                      "status": "400",
                      "code": "ENQ103",
                      "title": "Bad Request",
                      "detail": "Invalid format for field - vehicle registration number"
                    }
                ]
            }',
        ];

        yield 'Too Many Requests message' => [
            'expectedExceptionClass' => RequestRejectedWithMessage::class,
            'expectedExceptionMessage' => 'Request rejected by DVLA Vehicle Enquiry with message "Too Many Requests".',
            'statusCode' => 429,
            'responseBody' => '{
                  "message":"Too Many Requests"
                }',
        ];

        yield 'Unsupported JSON response with 4xx status code' => [
            'expectedExceptionClass' => RequestFailed::class,
            'expectedExceptionMessage' => 'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received 456.',
            'statusCode' => 456,
            'responseBody' => '{
                  "unsupported":"Too Many Requests"
                }',
        ];

        yield 'Not JSON response' => [
            'expectedExceptionClass' => RequestFailed::class,
            'expectedExceptionMessage' => 'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received 400, with invalid json.',
            'statusCode' => 400,
            'responseBody' => '<html><body>Error</body></html>',
        ];
    }

    /**
     * @psalm-param class-string<\Throwable> $expectedExceptionClass
     *
     * @test
     * @dataProvider providesApiInternalErrors
     */
    public function it_should_throw_exception_on_api_internal_error(
        string $expectedExceptionClass,
        string $expectedExceptionMessage,
        GuzzleException $httpClientException
    ): void {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException($httpClientException);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fixture->request(
            $this->createMock(UriInterface::class),
            HttpMethod::post()
        );
    }

    public function providesApiInternalErrors(): iterable
    {
        yield '500 status with error response body' => [
            'expectedExceptionClass' => RequestRejectedWithError::class,
            'expectedExceptionMessage' => 'Request rejected by DVLA Vehicle Enquiry with status 500, code ENQ108, title "Internal Server Error" and message "System Error occurred".',
            'httpClientException' => new ServerException(
                'Test exception message',
                $this->createMock(Request::class),
                new GuzzleResponse(
                    500,
                    [],
                    '{
                        "errors": [
                            {
                                "status": "500",
                                "code": "ENQ108",
                                "title": "Internal Server Error",
                                "detail": "System Error occurred"
                            }
                        ]
                    }'
                )
            ),
        ];

        yield '5xx status code with unsupported error format' => [
            'expectedExceptionClass' => RequestFailed::class,
            'expectedExceptionMessage' => 'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received 503.',
            'httpClientException' => new ServerException(
                'Test exception message',
                $this->createMock(Request::class),
                new GuzzleResponse(
                    503,
                    [],
                    '[
                        {
                            "status": 503,
                            "title": "System currently down for maintenance",
                            "detail": "The service is currently down for maintenance, please contact support for more information"
                        }
                    ]'
                )
            ),
        ];

        yield '5xx status code with unexpected error format' => [
            'expectedExceptionClass' => RequestFailed::class,
            'expectedExceptionMessage' => 'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received 567.',
            'httpClientException' => new ServerException(
                'Test exception message',
                $this->createMock(Request::class),
                new GuzzleResponse(
                    567,
                    [],
                    '{"test":1}'
                )
            ),
        ];

        yield 'Connection error' => [
            'expectedExceptionClass' => RequestFailed::class,
            'expectedExceptionMessage' => 'Communication failure with DVLA Vehicle Enquiry API.',
            'httpClientException' => new ConnectException(
                'Test exception message',
                $this->createMock(Request::class)
            ),
        ];
    }
}
