<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Frame response-header policies. */
enum FramePolicy: string
{
    case Deny = 'deny';
    case SameOrigin = 'sameorigin';
    case All = 'all';
}
