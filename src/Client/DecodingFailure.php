<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

use Lendable\Dvla\VehicleEnquiry\DvlaVehicleEnquiryFailure;

class DecodingFailure extends \RuntimeException implements DvlaVehicleEnquiryFailure
{
    private function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function dueTo(\Throwable $cause): self
    {
        return new self('Failed to decode response content.', 0, $cause);
    }

    /**
     * @param string|int|float|bool|array<mixed>|null $actualValue
     */
    public static function unexpectedType(string $expectedType, $actualValue): self
    {
        return new self(
            \sprintf(
                'Failed to decode response content, unexpected type decoded to. Expected %s, got %s.',
                $expectedType,
                \gettype($actualValue)
            )
        );
    }
}
