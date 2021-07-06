<?php

declare(strict_types=1);

namespace Tests\Functional\Lendable\Dvla\VehicleEnquiry;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Lendable\Dvla\VehicleEnquiry\Auth\ApiKeyAuthHttpClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Auth\ValueObject\ApiKey;
use Lendable\Dvla\VehicleEnquiry\Client;
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
    public function it_should_request_vehicle_details_with_the_given_registration_number(): void
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
}
