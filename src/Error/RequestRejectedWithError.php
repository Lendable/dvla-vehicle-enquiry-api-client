<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error;

use Lendable\Dvla\VehicleEnquiry\DvlaVehicleEnquiryFailure;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Error;

final class RequestRejectedWithError extends \RuntimeException implements DvlaVehicleEnquiryFailure
{
    private Error $error;

    private function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function of(Error $error, ?\Throwable $cause = null): self
    {
        $instance = new self(
            \sprintf(
                'Request rejected by DVLA Vehicle Enquiry with status %s, code %s, title "%s" and message "%s".',
                $error->getStatus(),
                $error->getCode(),
                $error->getTitle(),
                $error->getDetail(),
            ),
            0,
            $cause
        );
        $instance->error = $error;

        return $instance;
    }

    public function getError(): Error
    {
        return $this->error;
    }
}
