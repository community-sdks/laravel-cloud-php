<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration;

use DateTimeImmutable;
use DateTimeInterface;
use UnexpectedValueException;

/** Validates and narrows decoded JSON values before DTO construction. */
final class DataReader
{
    /** @return array<string, mixed> */
    public static function object(mixed $value, string $path): array
    {
        if (!is_array($value) || array_is_list($value)) {
            throw new UnexpectedValueException(sprintf('%s must be a JSON object.', $path));
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /** @return list<mixed> */
    public static function list(mixed $value, string $path): array
    {
        if (!is_array($value) || !array_is_list($value)) {
            throw new UnexpectedValueException(sprintf('%s must be a JSON array.', $path));
        }

        return $value;
    }

    public static function string(mixed $value, string $path): string
    {
        if (!is_string($value)) {
            throw new UnexpectedValueException(sprintf('%s must be a string.', $path));
        }

        return $value;
    }

    public static function nullableString(mixed $value, string $path): ?string
    {
        return null === $value ? null : self::string($value, $path);
    }

    public static function int(mixed $value, string $path): int
    {
        if (!is_int($value)) {
            throw new UnexpectedValueException(sprintf('%s must be an integer.', $path));
        }

        return $value;
    }

    public static function nullableInt(mixed $value, string $path): ?int
    {
        return null === $value ? null : self::int($value, $path);
    }

    /** Read an integer or floating-point JSON number as a float. */
    public static function number(mixed $value, string $path): float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new UnexpectedValueException(sprintf('%s must be a number.', $path));
        }

        return (float) $value;
    }

    public static function bool(mixed $value, string $path): bool
    {
        if (!is_bool($value)) {
            throw new UnexpectedValueException(sprintf('%s must be a boolean.', $path));
        }

        return $value;
    }

    /**
     * Read an object member whose JSON name may be converted to an integer PHP key.
     *
     * JSON object keys such as "429" become integer array keys when decoded in
     * associative mode, despite being strings in the API contract.
     *
     * @param array<string, mixed> $object
     */
    public static function member(array $object, string $key): mixed
    {
        foreach ($object as $member => $value) {
            if ((string) $member === $key) {
                return $value;
            }
        }

        return null;
    }

    public static function nullableDate(mixed $value, string $path): ?DateTimeImmutable
    {
        if (null === $value) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, self::string($value, $path));
        if (false === $date) {
            throw new UnexpectedValueException(sprintf('%s must be an ISO 8601 date-time.', $path));
        }

        return $date;
    }

    /**
     * @param array<string, mixed> $object
     *
     * @return array<string, mixed>|null
     */
    public static function optionalObject(array $object, string $key, string $path): ?array
    {
        return array_key_exists($key, $object) ? self::object($object[$key], $path) : null;
    }
}
