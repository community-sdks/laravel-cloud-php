<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Domains;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\SimplifiedDomainResource;

/** Typed paginated response for an environment's domains. */
final readonly class ListDomainsResponse
{
    /**
     * @param list<SimplifiedDomainResource> $data
     * @param list<EnvironmentResource>      $included
     */
    public function __construct(public array $data, public PaginationLinks $links, public PaginationMeta $meta, public array $included) {}
}
