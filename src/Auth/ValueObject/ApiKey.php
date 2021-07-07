<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Auth\ValueObject;

use ParagonIE\HiddenString\HiddenString;

class ApiKey
{
    private HiddenString $value;

    private function __construct()
    {
    }

    public static function fromString(string $token): self
    {
        $instance = new self();
        $instance->value = new HiddenString($token, true, true);

        return $instance;
    }

    public function toString(): string
    {
        return $this->value->getString();
    }

    public function equals(ApiKey $other): bool
    {
        return $this->value->equals($other->value);
    }
}
