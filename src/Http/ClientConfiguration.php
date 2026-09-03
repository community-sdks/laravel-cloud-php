<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Http;

use InvalidArgumentException;

/**
 * Immutable connection settings used to construct the HTTP transport.
 *
 * Validation happens here so invalid credentials, URLs, and timeouts fail
 * before an API request is attempted.
 */
final readonly class ClientConfiguration
{
    public const DEFAULT_BASE_URI = 'https://cloud.laravel.com/api/';

    public function __construct(
        public string $token,
        public string $baseUri = self::DEFAULT_BASE_URI,
        public float $timeout = 30.0,
        public float $connectTimeout = 10.0,
        public string $userAgent = 'community-sdks/laravel-cloud-php/dev',
    ) {
        if ('' === trim($this->token)) {
            throw new InvalidArgumentException('The Laravel Cloud API token cannot be empty.');
        }

        if (false === filter_var($this->baseUri, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('The Laravel Cloud base URI must be a valid URL.');
        }

        if ($this->timeout <= 0 || $this->connectTimeout <= 0) {
            throw new InvalidArgumentException('HTTP timeouts must be greater than zero.');
        }
    }

    /** Return the base URI with the trailing slash Guzzle needs for resolution. */
    public function normalizedBaseUri(): string
    {
        return rtrim($this->baseUri, '/') . '/';
    }
}
