<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use DateTimeImmutable;

/** Attributes returned for an application resource. */
final readonly class ApplicationAttributes
{
    public function __construct(
        public string $name,
        public string $slug,
        public CloudRegion $region,
        public ?string $rootDirectory,
        public ?string $slackChannel,
        public string $avatarUrl,
        public ?DateTimeImmutable $createdAt,
        public ?ApplicationRepository $repository,
    ) {}
}
