<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Edge cache strategies accepted for environments. */
enum CacheStrategy: string
{
    case Default = 'default';
    case Bypass = 'bypass';
}
