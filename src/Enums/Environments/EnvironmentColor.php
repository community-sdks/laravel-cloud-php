<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Colors supported by the environment UI. */
enum EnvironmentColor: string
{
    case Blue = 'blue';
    case Green = 'green';
    case Orange = 'orange';
    case Purple = 'purple';
    case Red = 'red';
    case Yellow = 'yellow';
    case Cyan = 'cyan';
    case Gray = 'gray';
}
