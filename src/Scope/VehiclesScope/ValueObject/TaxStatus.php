<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject;

enum TaxStatus: string
{
    case NOT_TAXED_FOR_ON_ROAD_USE = 'Not Taxed for on Road Use';
    case SORN = 'SORN';
    case TAXED = 'Taxed';
    case UNTAXED = 'Untaxed';
}
