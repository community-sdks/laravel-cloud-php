<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for PhpMajorVersion. */
enum PhpMajorVersion: string
{
    case Php82 = '8.2';
    case Php83 = '8.3';
    case Php84 = '8.4';
    case Php85 = '8.5';
}
