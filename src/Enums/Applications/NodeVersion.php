<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for NodeVersion. */
enum NodeVersion: string
{
    case Node20 = '20';
    case Node22 = '22';
    case Node24 = '24';
}
