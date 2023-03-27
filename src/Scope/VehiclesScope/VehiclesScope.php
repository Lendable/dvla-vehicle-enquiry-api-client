<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope;

use Lendable\Dvla\VehicleEnquiry\Scope\Scope;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Request\EnquiryRequest;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Response\EnquiryResponse;

final class VehiclesScope extends Scope
{
    protected static function pathFragment(): string
    {
        return 'vehicles';
    }

    public function enquireDetails(EnquiryRequest $request): EnquiryResponse
    {
        $responseData = $this->sendAndDecode($request);

        return EnquiryResponse::fromArray($responseData);
    }
}
