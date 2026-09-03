<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Relationships\Applications;

/** Flattened resource IDs from the relationships exposed by a deployment. */
final readonly class DeploymentRelationships
{
    public function __construct(
        public ?string $environmentId,
        public ?string $initiatorId,
    ) {}
}
