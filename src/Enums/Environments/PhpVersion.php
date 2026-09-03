<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Exact PHP version identifiers documented for environment updates. */
enum PhpVersion: string
{
    case Php82 = '8.2:1';
    case Php83 = '8.3:1';
    case Php84 = '8.4:1';
    case Php85 = '8.5:1';
}
