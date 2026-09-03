<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Relationships\Applications;

/** Flattened resource IDs from the relationships exposed by an application. */
final readonly class ApplicationRelationships
{
    /**
     * @param list<string>|null $environmentIds
     * @param list<string>|null $deploymentIds
     */
    public function __construct(
        public ?string $repositoryId,
        public ?string $organizationId,
        public ?array $environmentIds,
        public ?array $deploymentIds,
        public ?string $defaultEnvironmentId,
    ) {}
}
