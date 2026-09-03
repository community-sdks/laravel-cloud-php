<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Commands;

/** Relationships accepted by command includes. */ enum CommandInclude: string
{
    case Environment = 'environment';
    case Deployment = 'deployment';
    case Initiator = 'initiator';
}
