<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** HTTP Strict Transport Security response settings. */
final readonly class HstsSettings
{
    public function __construct(public int $maxAge, public bool $includeSubdomains, public bool $preload) {}
}
