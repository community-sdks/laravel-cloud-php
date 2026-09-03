<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when Laravel Cloud rejects the supplied Bearer token with HTTP 401. */
final class AuthenticationException extends ApiException {}
