<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\EdgeNetworks;

/** Edge-network tenancy modes. */ enum TenancyType: string
{
    case Shared = 'shared';
    case Dedicated = 'dedicated';
}
