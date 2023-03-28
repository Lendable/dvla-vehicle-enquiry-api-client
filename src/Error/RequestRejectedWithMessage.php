<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error;

use Lendable\Dvla\VehicleEnquiry\DvlaVehicleEnquiryFailure;
use Lendable\Dvla\VehicleEnquiry\Error\ValueObject\Message;

final class RequestRejectedWithMessage extends \RuntimeException implements DvlaVehicleEnquiryFailure
{
    private Message $responseMessage;

    private function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function of(Message $responseMessage, ?\Throwable $cause = null): self
    {
        $instance = new self(
            \sprintf(
                'Request rejected by DVLA Vehicle Enquiry with message "%s".',
                $responseMessage->toString(),
            ),
            0,
            $cause
        );
        $instance->responseMessage = $responseMessage;

        return $instance;
    }

    public function getResponseMessage(): Message
    {
        return $this->responseMessage;
    }
}
