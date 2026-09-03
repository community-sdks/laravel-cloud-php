<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Environments;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;

/** Typed deployment returned when starting an environment. */
final readonly class DeploymentResponse
{ /** @param list<EnvironmentResource> $included */ public function __construct(public DeploymentResource $data, public array $included) {}
}
