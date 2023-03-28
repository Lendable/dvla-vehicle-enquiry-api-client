<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error\ValueObject;

final class Message
{
    private function __construct(private readonly string $value)
    {
    }

    public static function fromString(string $message): self
    {
        return new self($message);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
