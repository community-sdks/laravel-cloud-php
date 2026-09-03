<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

use Throwable;

/**
 * Represents a non-success HTTP response returned by Laravel Cloud.
 *
 * The original status, response body, and API-provided message are retained
 * so callers can log or inspect the failure without depending on Guzzle.
 */
class ApiException extends LaravelCloudException
{
    public function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly string $responseBody,
        private readonly ?string $apiMessage = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /** Return the HTTP status code supplied by Laravel Cloud. */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** Return the unmodified response body for diagnostics. */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    /** Return the best structured error message found in the response. */
    public function getApiMessage(): ?string
    {
        return $this->apiMessage;
    }
}
