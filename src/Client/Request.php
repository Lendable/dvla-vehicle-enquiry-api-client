<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

use Lendable\Dvla\VehicleEnquiry\Client\ValueObject\HttpMethod;

interface Request
{
    public function method(): HttpMethod;
}
