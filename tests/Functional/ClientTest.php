<?php

declare(strict_types=1);

namespace Tests\Functional\Lendable\Dvla\VehicleEnquiry;

use Assert\InvalidArgumentException;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Lendable\Dvla\VehicleEnquiry\Auth\ApiKeyAuthHttpClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Auth\ValueObject\ApiKey;
use Lendable\Dvla\VehicleEnquiry\Client;
use Lendable\Dvla\VehicleEnquiry\Error\RequestFailed;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithError;
use Lendable\Dvla\VehicleEnquiry\Error\RequestRejectedWithMessage;
use Lendable\Dvla\VehicleEnquiry\GuzzleClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request\EnquiryRequest;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Response\EnquiryResponse;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\Date;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\MotStatus;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\TaxStatus;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\YearAndMonth;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private const BASE_URL = 'https://127.0.0.1/aaa/bbb/ccc';

    private const AUTH_TOKEN = 'ASD-123-456';

    /**
     * @var GuzzleHttpClient&MockObject
     */
    private GuzzleHttpClient $httpClient;

    private Client $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(GuzzleHttpClient::class);

        $this->fixture = new Client(
            new ApiKeyAuthHttpClientDecorator(
                new GuzzleClientDecorator(
                    $this->httpClient
                ),
                ApiKey::fromString(self::AUTH_TOKEN)
            ),
            new Uri(self::BASE_URL)
        );
    }

    /**
     * @test
     */
    public function it_should_request_vehicle_details_with_the_given_registration_number_and_decode_response(): void
    {
        $registrationNumber = 'BV65CXG';
        $request = EnquiryRequest::with(RegistrationNumber::fromString($registrationNumber));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                new Uri(self::BASE_URL . '/vehicles'),
                [
                    'json' => [
                        'registrationNumber' => $registrationNumber,
                    ],
                    'headers' => [
                        'x-api-key' => self::AUTH_TOKEN,
                    ],
                ]
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    <<<JSON_PAYLOAD
                        {
                            "registrationNumber": "BV65CXG",
                            "co2Emissions": 172,
                            "engineCapacity": 2198,
                            "markedForExport": false,
                            "fuelType": "DIESEL",
                            "motStatus": "Valid",
                            "revenueWeight": 3000,
                            "colour": "BLUE",
                            "make": "FORD",
                            "typeApproval": "M1",
                            "yearOfManufacture": 2015,
                            "taxDueDate": "2021-05-30",
                            "taxStatus": "Untaxed",
                            "dateOfLastV5CIssued": "2021-05-13",
                            "motExpiryDate": "2022-06-30",
                            "wheelplan": "2 AXLE RIGID BODY",
                            "monthOfFirstRegistration": "2015-09"
                        }
                        JSON_PAYLOAD
                )
            );

        $response = $this->fixture->vehicles()->enquireDetails($request);

        $this->assertInstanceOf(EnquiryResponse::class, $response);
        $this->assertSame($registrationNumber, $response->getRegistrationNumber()->toString());
        $this->assertSame(172, $response->getCo2Emissions());
        $this->assertSame(2198, $response->getEngineCapacity());
        $this->assertFalse($response->getMarkedForExport());
        $this->assertSame('DIESEL', $response->getFuelType());
        $this->assertSame(MotStatus::fromString(MotStatus::VALID), $response->getMotStatus());
        $this->assertSame(3000, $response->getRevenueWeight());
        $this->assertSame('BLUE', $response->getColour());
        $this->assertSame('FORD', $response->getMake());
        $this->assertSame('M1', $response->getTypeApproval());
        $this->assertSame(2015, $response->getYearOfManufacture());
        $this->assertEquals(Date::fromString('2021-05-30'), $response->getTaxDueDate());
        $this->assertSame(TaxStatus::fromString(TaxStatus::UNTAXED), $response->getTaxStatus());
        $this->assertEquals(Date::fromString('2021-05-13'), $response->getDateOfLastV5CIssued());
        $this->assertEquals(Date::fromString('2022-06-30'), $response->getMotExpiryDate());
        $this->assertSame('2 AXLE RIGID BODY', $response->getWheelplan());
        $this->assertEquals(YearAndMonth::fromString('2015-09'), $response->getMonthOfFirstRegistration());
        $this->assertNull($response->getMonthOfFirstDvlaRegistration());
        $this->assertNull($response->getRealDrivingEmissions());
        $this->assertNull($response->getEuroStatus());
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
            new Response($statusCode, [], $responseBody),
        );
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException($httpClientException);
        $request = EnquiryRequest::with(RegistrationNumber::fromString('ER19NFD'));

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fixture->vehicles()->enquireDetails($request);
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
        $request = EnquiryRequest::with(RegistrationNumber::fromString('ER19NFD'));

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fixture->vehicles()->enquireDetails($request);
    }

    public function providesApiInternalErrors(): iterable
    {
        yield '500 status with error response body' => [
            'expectedExceptionClass' => RequestRejectedWithError::class,
            'expectedExceptionMessage' => 'Request rejected by DVLA Vehicle Enquiry with status 500, code ENQ108, title "Internal Server Error" and message "System Error occurred".',
            'httpClientException' => new ServerException(
                'Test exception message',
                $this->createMock(Request::class),
                new Response(
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
                new Response(
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
                new Response(
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

    /**
     * @test
     * @dataProvider providesErrorResponses
     */
    public function it_should_throw_exception_on_unsupported_response(): void
    {
        $responseBody = '{
                  "unsupported":"Test"
                }';

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn(
                new Response(200, [], $responseBody)
            );
        $request = EnquiryRequest::with(RegistrationNumber::fromString('ER19NFD'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Array does not contain an element with key "registrationNumber"');

        $this->fixture->vehicles()->enquireDetails($request);
    }
}
