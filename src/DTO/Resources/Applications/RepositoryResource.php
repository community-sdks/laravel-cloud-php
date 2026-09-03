<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** JSON:API repository resource accepted in the included collection. */
final readonly class RepositoryResource
{
    public function __construct(
        public string $id,
        public ?RepositoryAttributes $attributes,
    ) {}
}
