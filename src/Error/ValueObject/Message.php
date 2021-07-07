<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error\ValueObject;

class Message
{
    private string $value;

    private function __construct()
    {
    }

    public static function fromString(string $message): self
    {
        $instance = new self();
        $instance->value = $message;

        return $instance;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
