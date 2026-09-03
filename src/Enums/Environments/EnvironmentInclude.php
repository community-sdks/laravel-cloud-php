<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Relationships accepted by environment include queries. */
enum EnvironmentInclude: string
{
    case Application = 'application';
    case Branch = 'branch';
    case Deployments = 'deployments';
    case CurrentDeployment = 'currentDeployment';
    case PrimaryDomain = 'primaryDomain';
    case Instances = 'instances';
    case Database = 'database';
    case Cache = 'cache';
    case Buckets = 'buckets';
    case WebsocketApplication = 'websocketApplication';
    case Secrets = 'secrets';
}
