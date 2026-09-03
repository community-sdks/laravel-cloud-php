<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for RateLimitPerMinute. */
enum RateLimitPerMinute: int
{
    case OneHundred = 100;
    case ThreeHundred = 300;
    case FiveHundred = 500;
    case SevenHundredFifty = 750;
    case OneThousand = 1000;
}
