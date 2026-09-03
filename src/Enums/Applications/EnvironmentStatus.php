<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for EnvironmentStatus. */
enum EnvironmentStatus: string
{
    case Deploying = 'deploying';
    case Running = 'running';
    case Hibernating = 'hibernating';
    case Stopped = 'stopped';
}
