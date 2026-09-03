<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;
use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\EnvironmentRelationships;

/** JSON:API environment resource accepted in the included collection. */
final readonly class EnvironmentResource
{
    public function __construct(
        public string $id,
        public ?EnvironmentAttributes $attributes,
        public ?EnvironmentRelationships $relationships,
        public Link $selfLink,
    ) {}
}
