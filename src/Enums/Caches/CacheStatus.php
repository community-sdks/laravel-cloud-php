<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Caches;

/** Cache lifecycle states. */ enum CacheStatus: string
{
    case Creating = 'creating';
    case Updating = 'updating';
    case Available = 'available';
    case Stopped = 'stopped';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Unknown = 'unknown';
}
