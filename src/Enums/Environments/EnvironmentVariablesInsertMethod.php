<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Merge behavior for adding environment variables. */
enum EnvironmentVariablesInsertMethod: string
{
    case Append = 'append';
    case Set = 'set';
}
