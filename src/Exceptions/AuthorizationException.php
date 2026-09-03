<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when the authenticated account cannot perform an operation (HTTP 403). */
final class AuthorizationException extends ApiException {}
