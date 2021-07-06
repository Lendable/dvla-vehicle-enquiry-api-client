<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

interface Request
{
    public function method(): HttpMethod;
}
