<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Environments;

/** Content-type response-header policies. */
enum ContentTypePolicy: string
{
    case NoSniff = 'nosniff';
    case None = 'none';
}
