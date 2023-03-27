<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error;

use Lendable\Dvla\VehicleEnquiry\DvlaVehicleEnquiryFailure;

final class RequestFailed extends \RuntimeException implements DvlaVehicleEnquiryFailure
{
    private function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function dueTo(\Throwable $throwable): self
    {
        return new self('Communication failure with DVLA Vehicle Enquiry API.', 0, $throwable);
    }

    public static function dueToInvalidStatusCode(int $statusCode): self
    {
        return new self(
            \sprintf(
                'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received %u.',
                $statusCode
            ),
            $statusCode
        );
    }

    public static function dueToInvalidJson(int $statusCode, ?\Throwable $previous = null): self
    {
        return new self(
            \sprintf(
                'Communication failure with DVLA Vehicle Enquiry API, expected status code 2xx, received %u, with invalid json.',
                $statusCode
            ),
            $statusCode,
            $previous
        );
    }
}
