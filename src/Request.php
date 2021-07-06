<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry;

use Lendable\Dvla\VehicleEnquiry\Client\HttpMethod;

interface Request
{
    public function method(): HttpMethod;
}
