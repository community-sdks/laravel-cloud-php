<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for CloudRegion. */
enum CloudRegion: string
{
    case UsEast2 = 'us-east-2';
    case UsEast1 = 'us-east-1';
    case CanadaCentral1 = 'ca-central-1';
    case EuropeCentral1 = 'eu-central-1';
    case EuropeWest1 = 'eu-west-1';
    case EuropeWest2 = 'eu-west-2';
    case MiddleEastCentral1 = 'me-central-1';
    case AsiaPacificSoutheast1 = 'ap-southeast-1';
    case AsiaPacificSoutheast2 = 'ap-southeast-2';
    case AsiaPacificNortheast1 = 'ap-northeast-1';
}
