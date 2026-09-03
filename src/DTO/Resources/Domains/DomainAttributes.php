<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainActionRequired;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainCloudflareStrategy;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainRedirect;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainStatus;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainType;
use DateTimeImmutable;

/** Complete attributes returned for an individual domain. */
final readonly class DomainAttributes
{
    public function __construct(public string $name, public DomainType $type, public DomainStatus $hostnameStatus, public DomainStatus $sslStatus, public DomainStatus $originStatus, public ?DomainRedirect $redirect, public ?DomainCloudflareStrategy $cloudflareStrategy, public ?bool $downtime, public bool $wildcardEnabled, public DomainDnsRecords $dnsRecords, public ?DomainVariant $wildcard, public ?DomainVariant $www, public ?DomainActionRequired $actionRequired, public ?DateTimeImmutable $lastVerifiedAt, public ?DateTimeImmutable $createdAt) {}
}
