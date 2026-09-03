<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitLevel;
use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitPerMinute;

/** Firewall rate-limit behavior, including the literal 4xx and 429 API flags. */
final readonly class RateLimitSettings
{
    public function __construct(
        public bool $respondTo429,
        public RateLimitLevel $level,
        public RateLimitPerMinute $perMinute,
        public bool $respondTo4xx,
    ) {}
}
