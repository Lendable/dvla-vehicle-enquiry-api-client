<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

interface PayloadRequest extends Request
{
    /**
     * @return array<string|int, mixed>
     */
    public function payload(): array;
}
