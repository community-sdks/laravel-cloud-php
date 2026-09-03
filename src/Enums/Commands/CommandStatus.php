<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Commands;

/** Remote command lifecycle states. */ enum CommandStatus: string
{
    case Pending = 'pending';
    case Created = 'command.created';
    case Running = 'command.running';
    case Failure = 'command.failure';
    case Success = 'command.success';
}
