<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Applications;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryResource;

/** Typed paginated response returned by GET /applications. */
final readonly class ListApplicationsResponse
{
    /**
     * @param list<ApplicationResource> $data
     * @param list<RepositoryResource|OrganizationResource|EnvironmentResource|DeploymentResource> $included
     */
    public function __construct(
        public array $data,
        public PaginationLinks $links,
        public PaginationMeta $meta,
        public array $included,
    ) {}
}
