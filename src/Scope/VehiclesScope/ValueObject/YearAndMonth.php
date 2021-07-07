<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class YearAndMonth
{
    private string $value;

    private function __construct()
    {
    }

    public static function fromString(string $date): self
    {
        Assert::that($date)
            ->date('Y-m');

        $instance = new self();
        $instance->value = $date;

        return $instance;
    }

    public function toDateTime(): \DateTimeImmutable
    {
        $date = new \DateTimeImmutable(\sprintf('%s-01', $this->value));

        return $date->setTime(0, 0);
    }
}
