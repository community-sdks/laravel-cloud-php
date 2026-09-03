<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for DeploymentStatus. */
enum DeploymentStatus: string
{
    case Pending = 'pending';
    case BuildPending = 'build.pending';
    case BuildCreated = 'build.created';
    case BuildQueued = 'build.queued';
    case BuildRunning = 'build.running';
    case BuildSucceeded = 'build.succeeded';
    case BuildFailed = 'build.failed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case DeploymentPending = 'deployment.pending';
    case DeploymentCreated = 'deployment.created';
    case DeploymentQueued = 'deployment.queued';
    case DeploymentRunning = 'deployment.running';
    case DeploymentSucceeded = 'deployment.succeeded';
    case DeploymentFailed = 'deployment.failed';
}
