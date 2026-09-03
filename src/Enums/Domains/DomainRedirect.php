<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Supported root and WWW redirect directions. */
enum DomainRedirect: string
{
    case RootToWww = 'root_to_www';
    case WwwToRoot = 'www_to_root';
}
