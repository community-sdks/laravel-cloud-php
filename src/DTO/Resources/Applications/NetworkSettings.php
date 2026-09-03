<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Complete network behavior attached to environment attributes. */
final readonly class NetworkSettings
{
    public function __construct(
        public string $cacheStrategy,
        public ResponseHeaderSettings $responseHeaders,
        public FirewallSettings $firewall,
        public bool $contentConverter,
    ) {}
}
