<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Relationships accepted by domain include query parameters. */
enum DomainInclude: string
{
    case Environment = 'environment';
}
