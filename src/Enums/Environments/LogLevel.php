<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Normalized Laravel Cloud log severity. */
enum LogLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Debug = 'debug';
}
