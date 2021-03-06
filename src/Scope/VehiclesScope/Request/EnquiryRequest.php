<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request;

use Lendable\Dvla\VehicleEnquiry\Client\PayloadRequest;
use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;

class EnquiryRequest implements PayloadRequest
{
    private RegistrationNumber $registrationNumber;

    private function __construct()
    {
    }

    public static function with(
        RegistrationNumber $registrationNumber
    ): self {
        $instance = new self();
        $instance->registrationNumber = $registrationNumber;

        return $instance;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::post();
    }

    /**
     * @return array{registrationNumber: string}
     */
    public function payload(): array
    {
        return [
            'registrationNumber' => $this->registrationNumber->toString(),
        ];
    }
}
