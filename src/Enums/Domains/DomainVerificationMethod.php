<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Supported domain verification workflows. */
enum DomainVerificationMethod: string
{
    case PreVerification = 'pre_verification';
    case RealTime = 'real_time';
}
