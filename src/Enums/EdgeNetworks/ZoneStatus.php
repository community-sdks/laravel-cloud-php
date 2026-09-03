<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\EdgeNetworks;

/** Edge-network lifecycle states. */ enum ZoneStatus: string
{
    case Requesting = 'requesting';
    case Creating = 'creating';
    case Available = 'available';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Unknown = 'unknown';
}
