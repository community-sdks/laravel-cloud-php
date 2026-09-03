<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Attributes returned for an included organization. */
final readonly class OrganizationAttributes
{
    public function __construct(public string $name, public string $slug) {}
}
