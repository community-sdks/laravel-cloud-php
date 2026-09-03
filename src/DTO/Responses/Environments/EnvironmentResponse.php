<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Environments;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryResource;

/** Typed document returned by single-environment operations. */
final readonly class EnvironmentResponse
{
    /** @param list<ApplicationResource|RepositoryResource|OrganizationResource|EnvironmentResource|DeploymentResource> $included */
    public function __construct(public EnvironmentResource $data, public array $included) {}
}
