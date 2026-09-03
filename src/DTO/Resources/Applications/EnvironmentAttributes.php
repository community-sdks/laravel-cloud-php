<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\EnvironmentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\NodeVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\PhpMajorVersion;
use DateTimeImmutable;

/** All documented attributes of an included environment resource. */
final readonly class EnvironmentAttributes
{
    public function __construct(
        public string $name,
        public string $slug,
        public EnvironmentStatus $status,
        public bool $createdFromAutomation,
        public string $vanityDomain,
        public PhpMajorVersion $phpMajorVersion,
        public ?string $buildCommand,
        public NodeVersion $nodeVersion,
        public ?string $deployCommand,
        public bool $usesOctane,
        public bool $usesHibernation,
        public ?int $hibernationWakeUpInterval,
        public bool $usesPushToDeploy,
        public bool $usesDeployHook,
        public ?EnvironmentVariables $environmentVariables,
        public NetworkSettings $networkSettings,
        public ?DateTimeImmutable $createdAt,
    ) {}
}
