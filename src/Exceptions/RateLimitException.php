<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when Laravel Cloud throttles the client with HTTP 429. */
final class RateLimitException extends ApiException {}
