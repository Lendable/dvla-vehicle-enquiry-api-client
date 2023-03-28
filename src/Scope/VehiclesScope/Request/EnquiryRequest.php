<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request;

use Lendable\Dvla\VehicleEnquiry\Client\PayloadRequest;
use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\HttpMethod;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;

final class EnquiryRequest implements PayloadRequest
{
    private function __construct(private readonly RegistrationNumber $registrationNumber)
    {
    }

    public static function with(RegistrationNumber $registrationNumber): self
    {
        return new self($registrationNumber);
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
