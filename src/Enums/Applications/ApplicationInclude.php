<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for ApplicationInclude. */
enum ApplicationInclude: string
{
    case Organization = 'organization';
    case Environments = 'environments';
    case DefaultEnvironment = 'defaultEnvironment';
}
