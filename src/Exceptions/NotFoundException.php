<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when a requested Laravel Cloud resource does not exist (HTTP 404). */
final class NotFoundException extends ApiException {}
