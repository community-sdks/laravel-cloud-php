<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Normalized source category returned for a log event. */
enum NormalizedLogType: string
{
    case Access = 'access';
    case Application = 'application';
    case Exception = 'exception';
    case System = 'system';
}
