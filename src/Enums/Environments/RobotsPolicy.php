<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Robots response-header policies. */
enum RobotsPolicy: string
{
    case IndexFollow = 'index, follow';
    case NoIndexNoFollow = 'noindex, nofollow';
}
