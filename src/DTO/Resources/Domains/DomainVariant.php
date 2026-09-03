<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainStatus;

/** Status and DNS requirements for a wildcard or WWW domain variant. */
final readonly class DomainVariant
{
    public function __construct(public DomainStatus $hostnameStatus, public DomainStatus $sslStatus, public DomainStatus $originStatus, public DomainDnsRecords $dnsRecords) {}
}
