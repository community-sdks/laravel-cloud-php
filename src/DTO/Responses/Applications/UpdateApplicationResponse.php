<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Applications;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryResource;

/** Typed JSON:API document returned after updating an application. */
final readonly class UpdateApplicationResponse
{
    /** @param list<RepositoryResource|OrganizationResource|EnvironmentResource|DeploymentResource> $included */
    public function __construct(public ApplicationResource $data, public array $included) {}
}
