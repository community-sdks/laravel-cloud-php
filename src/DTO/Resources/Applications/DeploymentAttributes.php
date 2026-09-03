<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\DeploymentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\NodeVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\PhpMajorVersion;
use DateTimeImmutable;

/** All documented attributes of an included deployment resource. */
final readonly class DeploymentAttributes
{
    public function __construct(
        public DeploymentStatus $status,
        public string $branchName,
        public string $commitHash,
        public string $commitMessage,
        public ?string $commitAuthor,
        public ?string $failureReason,
        public PhpMajorVersion $phpMajorVersion,
        public ?string $buildCommand,
        public NodeVersion $nodeVersion,
        public bool $usesOctane,
        public bool $usesHibernation,
        public ?int $hibernationWakeUpInterval,
        public ?DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $finishedAt,
    ) {}
}
