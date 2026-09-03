<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Time windows supported by environment metrics. */
enum MetricPeriod: string
{
    case SixHours = '6h';
    case TwentyFourHours = '24h';
    case ThreeDays = '3d';
    case SevenDays = '7d';
    case ThirtyDays = '30d';
}
