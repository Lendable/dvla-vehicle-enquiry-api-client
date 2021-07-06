<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class Date
{
    private string $date;

    private function __construct()
    {
    }

    public static function fromString(string $date): self
    {
        Assert::that($date)
            ->date('Y-m-d');

        $instane = new self();
        $instane->date = $date;

        return $instane;
    }

    public function toDateTime(): \DateTimeImmutable
    {
        $date = new \DateTimeImmutable($this->date);

        return $date->setTime(0, 0);
    }
}
