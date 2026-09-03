<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\ApplicationRelationships;

/** JSON:API application resource. Attributes and relationships are optional in OpenAPI. */
final readonly class ApplicationResource
{
    public function __construct(
        public string $id,
        public ?ApplicationAttributes $attributes,
        public ?ApplicationRelationships $relationships,
    ) {}
}
