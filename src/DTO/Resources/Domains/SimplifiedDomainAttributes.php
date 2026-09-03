<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainRedirect;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainStatus;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainType;
use DateTimeImmutable;

/** Attributes returned by the paginated domain listing. */
final readonly class SimplifiedDomainAttributes
{
    public function __construct(public string $name, public DomainType $type, public DomainStatus $hostnameStatus, public DomainStatus $sslStatus, public DomainStatus $originStatus, public ?DomainRedirect $redirect, public ?DateTimeImmutable $lastVerifiedAt, public ?DateTimeImmutable $createdAt) {}
}
