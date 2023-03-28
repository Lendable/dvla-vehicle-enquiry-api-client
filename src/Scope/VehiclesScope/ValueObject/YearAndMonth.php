<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

final class YearAndMonth
{
    private function __construct(private readonly string $value)
    {
        Assert::that($this->value)
            ->date('Y-m');
    }

    public static function fromString(string $date): self
    {
        return new self($date);
    }

    public function toDateTime(): \DateTimeImmutable
    {
        $date = new \DateTimeImmutable(\sprintf('%s-01', $this->value));

        return $date->setTime(0, 0);
    }
}
