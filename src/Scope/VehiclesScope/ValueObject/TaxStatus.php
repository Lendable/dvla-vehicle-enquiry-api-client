<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class TaxStatus
{
    public const NOT_TAXED_FOR_ON_ROAD_USE = 'Not Taxed for on Road Use';
    public const SORN = 'SORN';
    public const TAXED = 'Taxed';
    public const UNTAXED = 'Untaxed';

    private const ALL = [
        self::NOT_TAXED_FOR_ON_ROAD_USE => 1,
        self::SORN => 1,
        self::TAXED => 1,
        self::UNTAXED => 1,
    ];

    private string $value;

    /**
     * @var array<string, self>
     */
    private static array $lazyLoad = [];

    private function __construct(string $value)
    {
        Assert::that(self::ALL)->keyIsset($value);

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return self::lazyLoad($value);
    }

    private static function lazyLoad(string $value): self
    {
        if (isset(self::$lazyLoad[$value])) {
            return self::$lazyLoad[$value];
        }

        return self::$lazyLoad[$value] = new self($value);
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
