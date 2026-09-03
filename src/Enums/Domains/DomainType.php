<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Domain hostname variants returned by Laravel Cloud. */
enum DomainType: string
{
    case Root = 'root';
    case Www = 'www';
    case Wildcard = 'wildcard';
}
