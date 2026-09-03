<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Applications\SourceControlProviderType;
use InvalidArgumentException;

/** Request body for creating a Laravel Cloud application. */
final readonly class CreateApplicationRequest
{
    /**
     * @param SourceControlProviderType $sourceControlProviderType Source control provider. Required since March 9, 2026.
     * @param string                    $repository                Repository understood by the selected provider.
     * @param string                    $name                      Application name containing 3 to 40 permitted characters.
     * @param CloudRegion               $region                    Laravel Cloud region in which to create the application.
     * @param string|null               $rootDirectory             Optional repository subdirectory, or null for its root.
     * @param string|null               $clusterId                 Dedicated cluster ID, only when the organization uses one.
     */
    public function __construct(
        public SourceControlProviderType $sourceControlProviderType,
        public string $repository,
        public string $name,
        public CloudRegion $region,
        public ?string $rootDirectory = null,
        public ?string $clusterId = null,
    ) {
        $nameLength = preg_match_all('/./u', $this->name);
        if (1 !== preg_match("/^[\\p{Latin}0-9 _.,'\\-&()!#+:@]+$/u", $this->name)
            || false === $nameLength
            || $nameLength < 3
            || $nameLength > 40) {
            throw new InvalidArgumentException('Application name must be 3 to 40 characters and match the Laravel Cloud naming rules.');
        }

        if (null !== $this->rootDirectory
            && (strlen($this->rootDirectory) > 255
                || 1 !== preg_match('#^[a-zA-Z0-9][-a-zA-Z0-9_.]*(?:/[a-zA-Z0-9][-a-zA-Z0-9_.]*)*$#', $this->rootDirectory))) {
            throw new InvalidArgumentException('Root directory must match the Laravel Cloud repository path rules.');
        }
    }

    /** @return array{source_control_provider_type: string, repository: string, name: string, region: string, root_directory: string|null, cluster_id: string|null} */
    public function toArray(): array
    {
        return [
            'source_control_provider_type' => $this->sourceControlProviderType->value,
            'repository' => $this->repository,
            'name' => $this->name,
            'region' => $this->region->value,
            'root_directory' => $this->rootDirectory,
            'cluster_id' => $this->clusterId,
        ];
    }
}
