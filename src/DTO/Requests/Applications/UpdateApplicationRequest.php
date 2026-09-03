<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\SourceControlProviderType;
use InvalidArgumentException;

/** Optional fields accepted when updating an application. */
final readonly class UpdateApplicationRequest
{
    public function __construct(
        public ?SourceControlProviderType $sourceControlProviderType = null,
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $defaultEnvironmentId = null,
        public ?string $repository = null,
        public ?string $slackChannel = null,
        public bool $clearSlackChannel = false,
    ) {
        $nameLength = null === $name ? null : preg_match_all('/./u', $name);
        if (null !== $name && (false === $nameLength || $nameLength < 3 || $nameLength > 40)) {
            throw new InvalidArgumentException('Application name must contain between 3 and 40 characters.');
        }
        if (null !== $slug && strlen($slug) < 3) {
            throw new InvalidArgumentException('Application slug must contain at least 3 characters.');
        }
        if (null !== $slackChannel && $clearSlackChannel) {
            throw new InvalidArgumentException('Slack channel cannot be set and cleared in the same request.');
        }
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        $data = [];
        if (null !== $this->sourceControlProviderType) {
            $data['source_control_provider_type'] = $this->sourceControlProviderType->value;
        }
        foreach ([
            'name' => $this->name,
            'slug' => $this->slug,
            'default_environment_id' => $this->defaultEnvironmentId,
            'repository' => $this->repository,
            'slack_channel' => $this->slackChannel,
        ] as $key => $value) {
            if (null !== $value) {
                $data[$key] = $value;
            }
        }
        if ($this->clearSlackChannel) {
            $data['slack_channel'] = null;
        }

        return $data;
    }
}
