<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

enum MotStatus: string
{
    case NO_DETAILS_HELD = 'No details held by DVLA';
    case NO_RESULT = 'No results returned';
    case NOT_VALID = 'Not valid';
    case VALID = 'Valid';
}
