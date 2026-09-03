<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Verification states used for hostname, SSL, and origin checks. */
enum DomainStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Failed = 'failed';
    case Disabled = 'disabled';
}
