<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when Laravel Cloud responds with an HTTP 5xx server failure. */
final class ServerException extends ApiException {}
