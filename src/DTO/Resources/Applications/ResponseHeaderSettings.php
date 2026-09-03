<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Security-related response header settings for an environment. */
final readonly class ResponseHeaderSettings
{
    public function __construct(public string $frame, public string $contentType, public HstsSettings $hsts) {}
}
