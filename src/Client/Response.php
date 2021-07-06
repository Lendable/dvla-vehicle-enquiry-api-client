<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client;

use Assert\Assert;

class Response
{
    private int $statusCode;

    /**
     * @var array<string, array<string>>
     */
    private array $headers;

    /**
     * @var array<string, array<string>>
     */
    private array $normalizedHeaders = [];

    private Content $content;

    /**
     * @param array<string, array<string>> $headers
     */
    private function __construct(int $statusCode, array $headers, Content $content)
    {
        // Non-2xx range are modelled as exceptions.
        Assert::that($statusCode)->range(200, 299);

        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->headers = $headers;

        foreach ($this->headers as $header => $value) {
            $this->normalizedHeaders[\strtolower($header)] = $value;
        }
    }

    /**
     * @param array<string, array<string>> $headers
     */
    public static function with(int $statusCode, array $headers, Content $content): self
    {
        return new self($statusCode, $headers, $content);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function content(): Content
    {
        return $this->content;
    }

    /**
     * @return array<string, array<string>>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, array<string>>
     */
    public function normalizedHeaders(): array
    {
        return $this->normalizedHeaders;
    }

    /**
     * @return array<string>
     */
    public function header(string $name): array
    {
        $normalizedName = \strtolower($name);

        if (isset($this->normalizedHeaders[$normalizedName])) {
            return $this->normalizedHeaders[$normalizedName];
        }

        throw new \InvalidArgumentException(\sprintf('Header with name "%s" does not exist.', $name));
    }
}
