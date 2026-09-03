<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

/** Thrown when a request cannot reach Laravel Cloud or its response cannot be decoded. */
final class TransportException extends LaravelCloudException {}
