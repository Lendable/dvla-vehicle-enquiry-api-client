<?php

declare(strict_types=1);

namespace Tests\Integration\Lendable\Dvla\VehicleEnquiry;

use Assert\Assert;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Uri;
use Lendable\Dvla\VehicleEnquiry\Auth\ApiKeyAuthHttpClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Auth\ValueObject\ApiKey;
use Lendable\Dvla\VehicleEnquiry\Client;
use Lendable\Dvla\VehicleEnquiry\GuzzleClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request\EnquiryRequest;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Response\EnquiryResponse;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private function createFixture(): Client
    {
        $configPath = __DIR__ .'/../../config_test.json';
        Assert::that($configPath)
            ->file(\sprintf('Please copy the config_test.dist.json to the %s path.', $configPath))
            ->readable('The config_test.json file must be readable.');
        $config = \file_get_contents($configPath);
        Assert::that($config)->isJsonString();
        $config = \json_decode($config, true);

        Assert::that($config)
            ->isArray('Make sure the The config_test.json file contains valid JSON as like the config_test.dist.json.')
            ->keyExists('baseUri')
            ->keyExists('token');

        return new Client(
            new ApiKeyAuthHttpClientDecorator(
                new GuzzleClientDecorator(
                    new GuzzleHttpClient()
                ),
                ApiKey::fromString($config['token'])
            ),
            new Uri($config['baseUri'])
        );
    }

    /**
     * @test
     */
    public function it_should_request_vehicle_details_from_the_dvla_vehicle_enquiy_api(): void
    {
        $registrationNumber = 'BV65CXG';
        $fixture = $this->createFixture();
        $request = EnquiryRequest::with(RegistrationNumber::fromString($registrationNumber));

        $response = $fixture->vehicles()->enquireDetails($request);

        $this->assertInstanceOf(EnquiryResponse::class, $response);
        $this->assertSame($registrationNumber, $response->getRegistrationNumber()->toString());
        $this->assertSame(2015, $response->getYearOfManufacture());
    }
}
