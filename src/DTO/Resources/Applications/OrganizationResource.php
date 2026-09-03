<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** JSON:API organization resource accepted in the included collection. */
final readonly class OrganizationResource
{
    public function __construct(
        public string $id,
        public ?OrganizationAttributes $attributes,
    ) {}
}
