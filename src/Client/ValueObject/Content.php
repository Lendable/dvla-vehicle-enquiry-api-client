<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client\ValueObject;

use Lendable\Dvla\VehicleEnquiry\Client\DecodingFailure;

final class Content
{
    private function __construct(private readonly string $value)
    {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function empty(): self
    {
        return new self('');
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    /**
     * @throws DecodingFailure
     */
    public function decode(): array
    {
        try {
            $decoded = \json_decode($this->value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw DecodingFailure::dueTo($exception);
        }

        if (!\is_array($decoded)) {
            throw DecodingFailure::unexpectedType('array', $decoded);
        }

        return $decoded;
    }
}
