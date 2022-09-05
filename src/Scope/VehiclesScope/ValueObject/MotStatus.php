<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class MotStatus
{
    public const NO_DETAILS_HELD = 'No details held by DVLA';
    public const NO_RESULT = 'No results returned';
    public const NOT_VALID = 'Not valid';
    public const VALID = 'Valid';

    private const ALL = [
        self::NO_DETAILS_HELD => 1,
        self::NO_RESULT => 1,
        self::NOT_VALID => 1,
        self::VALID => 1,
    ];

    /**
     * @var array<string, self>
     */
    private static array $lazyLoad = [];

    private function __construct(private string $value)
    {
        Assert::that(self::ALL)->keyIsset($this->value);
    }

    public static function fromString(string $value): self
    {
        return self::lazyLoad($value);
    }

    private static function lazyLoad(string $value): self
    {
        return self::$lazyLoad[$value] ??= new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
