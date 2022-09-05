<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Client\ValueObject;

class HttpMethod
{
    private const GET = 'GET';

    private const POST = 'POST';

    private const PUT = 'PUT';

    private const PATCH = 'PATCH';

    private const DELETE = 'DELETE';

    /**
     * @var array<string, HttpMethod>
     */
    private static array $lazyLoad = [];

    private function __construct(private string $value)
    {
    }

    public static function get(): self
    {
        return self::lazyLoad(self::GET);
    }

    public static function post(): self
    {
        return self::lazyLoad(self::POST);
    }

    public static function patch(): self
    {
        return self::lazyLoad(self::PATCH);
    }

    public static function put(): self
    {
        return self::lazyLoad(self::PUT);
    }

    public static function delete(): self
    {
        return self::lazyLoad(self::DELETE);
    }

    private static function lazyLoad(string $value): self
    {
        return self::$lazyLoad[$value] ??= new self($value);
    }

    public function equals(HttpMethod $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
