<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Domains;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DomainResource;

/** Typed document returned by single-domain mutation and retrieval endpoints. */
final readonly class DomainResponse
{
    /** @param list<EnvironmentResource> $included */
    public function __construct(public DomainResource $data, public array $included) {}
}
