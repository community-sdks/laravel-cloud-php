<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;
use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\DeploymentRelationships;

/** JSON:API deployment resource accepted in the included collection. */
final readonly class DeploymentResource
{
    public function __construct(
        public string $id,
        public ?DeploymentAttributes $attributes,
        public ?DeploymentRelationships $relationships,
        public Link $selfLink,
    ) {}
}
