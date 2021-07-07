<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class RegistrationNumber
{
    private string $value;

    private function __construct()
    {
    }

    public static function fromString(
        string $registrationNumber
    ): self {
        Assert::that($registrationNumber)->notEmpty('Registration number should not be empty.');

        $instance = new self();
        $instance->value = $registrationNumber;

        return $instance;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
