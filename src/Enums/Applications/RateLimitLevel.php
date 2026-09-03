<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for RateLimitLevel. */
enum RateLimitLevel: string
{
    case Challenge = 'challenge';
    case Throttle = 'throttle';
    case Ban = 'ban';
}
