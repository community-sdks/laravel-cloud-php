<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Relationships\Applications;

/** Flattened resource IDs from the relationships exposed by an environment. */
final readonly class EnvironmentRelationships
{
    /**
     * @param list<string>|null $deploymentIds
     * @param list<string>|null $domainIds
     * @param list<string>|null $instanceIds
     * @param list<string>|null $bucketIds
     * @param list<string>|null $secretIds
     */
    public function __construct(
        public ?string $applicationId,
        public ?string $branchId,
        public ?array $deploymentIds,
        public ?string $currentDeploymentId,
        public ?array $domainIds,
        public ?string $primaryDomainId,
        public ?array $instanceIds,
        public ?string $databaseId,
        public ?string $cacheId,
        public ?array $bucketIds,
        public ?string $websocketApplicationId,
        public ?array $secretIds,
    ) {}
}
