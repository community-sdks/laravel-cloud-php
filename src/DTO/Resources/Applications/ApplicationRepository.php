<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Repository summary embedded in application attributes. */
final readonly class ApplicationRepository
{
    public function __construct(
        public string $fullName,
        public string $defaultBranch,
    ) {}
}
