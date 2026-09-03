<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Attributes returned for an included repository. */
final readonly class RepositoryAttributes
{
    public function __construct(public string $name) {}
}
