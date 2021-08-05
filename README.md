DVLA Vehicle Enquiry API client
====

[![Latest Stable Version](https://poser.pugx.org/lendable/dvla-vehicle-enquiry-api-client/v/stable)](https://packagist.org/packages/lendable/dvla-vehicle-enquiry-api-client)
[![License](https://poser.pugx.org/lendable/dvla-vehicle-enquiry-api-client/license)](https://packagist.org/packages/lendable/dvla-vehicle-enquiry-api-client)

PHP client implementation for the DVLA Vehicle Enquiry API v1. This package provides:
- API client that supports PSR-18 HTTP clients
- token-based authentication
- value objects for the request building and for the response

## Installation
You can install the library via [Composer](https://getcomposer.org/).

```bash
composer require lendable/dvla-vehicle-enquiry-api-client
```

## Usage

The [Client](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Client.php) class implements the 
DVLA's REST API and can return the [vehicles scope](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Scope/VehiclesScope/VehiclesScope.php) which can be used to request the 
vehicle details.

For the instantiation of the [Client](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Client.php) class 
we need to inject the decorators which adds the API key authentication, and the PSR-18 compatibility layers.
Also, we need to define the API's base URI to easily switch between UAT and live service.

### Base URI

The [API's specification](https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service/v1.1.0-vehicle-enquiry-service.html#vehicle-enquiry-api) contains the UAT and live URL.
The given URI should not end with slash (`/`) and should contain the `/v1` path too.

For example: `https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1`

The client accepts the URI in any [PSR-7](https://www.php-fig.org/psr/psr-7/) UriInterface implementation.

### Authentication

The [ApiKeyAuthHttpClientDecorator](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Auth/ApiKeyAuthHttpClientDecorator.php) adds the required 
API token authentication headers to the requests.
The [ApiKey](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Auth/ValueObject/ApiKey.php) value 
object keeps the token secret, avoiding accidental exposure.

### HTTP client

With the [Psr18ClientDecorator](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/src/Psr18ClientDecorator.php)
you can use any HTTP client which supports the [PSR-18](https://www.php-fig.org/psr/psr-18/) standard to perform the prebuilt HTTP request. 

If you prefer to use an HTTP client that doesn't support the PSR-18 standard, you can alternatively make a simple decorator that calls the HTTP client 
using the PSR-18 RequestInterface format request data and convert the HTTP client's response to a PSR-18 ResponseInterface format response.

For example in our [integration test](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/tests/Integration/ClientTest.php) 
we are using GuzzleClient with a [decorator](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/blob/main/tests/Integration/Tool/GuzzlePsr18ClientDecorator.php) which using this PSR-18 conversion.

### Example request

```php
<?php

declare(strict_types=1);

use Lendable\Dvla\VehicleEnquiry\Auth\ApiKeyAuthHttpClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Auth\ValueObject\ApiKey;
use Lendable\Dvla\VehicleEnquiry\Client;
use Lendable\Dvla\VehicleEnquiry\Psr18ClientDecorator;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request\EnquiryRequest;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;
use Nyholm\Psr7\Uri;

$client = new Client(
        new ApiKeyAuthHttpClientDecorator(
            new Psr18ClientDecorator(
                new YourPsr18HttpClient()
            ),
            ApiKey::fromString('YOUR-AUTHENTICATION-TOKEN')
        ),
        new Uri('https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1')
    );

$vehicleDetails = $client->vehicles()->enquireDetails(
        EnquiryRequest::with(RegistrationNumber::fromString('AA19PPP'))
    );
```

This makes an API request with the `AA19PPP` registration number and the `$vehicleDetails` variable will contain an
[EnquiryResponse](https://github.com/Lendable/dvla-vehicle-enquiry-api-client/tree/main/src/Scope/VehiclesScope/Response) object 
which contains all the returned API response data in value objects.

This example is using the UAT API URL and a test registration number. [Test registration numbers](https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service/mock-responses.html#test-vrns)
for mock responses of the different test cases are available in the [DVLA Vehicle Enquiry Service API documentation](https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service/vehicle-enquiry-service-description.html#vehicle-enquiry-service-api).
