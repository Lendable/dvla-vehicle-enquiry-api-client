<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

use Assert\Assert;

class Date
{
    private function __construct(private string $date)
    {
        Assert::that($this->date)
            ->date('Y-m-d');
    }

    public static function fromString(string $date): self
    {
        return new self($date);
    }

    public function toDateTime(): \DateTimeImmutable
    {
        $date = new \DateTimeImmutable($this->date);

        return $date->setTime(0, 0);
    }
}
