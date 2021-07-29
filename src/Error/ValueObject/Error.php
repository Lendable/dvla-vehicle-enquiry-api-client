<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Error\ValueObject;

use Assert\Assert;

class Error
{
    private string $status;

    private string $code;

    private string $title;

    private string $detail;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        Assert::that($data)
            ->keyExists('status')
            ->keyExists('code')
            ->keyExists('title')
            ->keyExists('detail');

        $instance = new self();
        $instance->status = $data['status'];
        $instance->code = $data['code'];
        $instance->title = $data['title'];
        $instance->detail = $data['detail'];

        return $instance;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }
}
