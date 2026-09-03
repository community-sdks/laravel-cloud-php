<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Log categories accepted by the logs endpoint. */
enum LogType: string
{
    case All = 'all';
    case Application = 'application';
    case Access = 'access';
}
