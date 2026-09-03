<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Http;

use JsonException;
use UnexpectedValueException;

/**
 * Decodes JSON object responses at the SDK's untyped HTTP boundary.
 *
 * Endpoint hydrators consume the returned map and narrow every documented
 * value before constructing strongly typed public DTOs.
 */
final class ResponseDecoder
{
    /**
     * Decode a response body as a JSON object.
     *
     *
     * @throws UnexpectedValueException When the body is invalid JSON or is not an object.
     * @return array<string, mixed>
     */
    public function decode(string $body): array
    {
        if ('' === trim($body)) {
            return [];
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new UnexpectedValueException('Laravel Cloud returned invalid JSON.', 0, $exception);
        }

        if (!is_array($decoded) || array_is_list($decoded)) {
            throw new UnexpectedValueException('Laravel Cloud returned a JSON value that is not an object.');
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
