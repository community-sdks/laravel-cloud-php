<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;

/** Domain resource shape returned by the list endpoint. */
final readonly class SimplifiedDomainResource
{
    public function __construct(public string $id, public ?SimplifiedDomainAttributes $attributes, public ?string $environmentId, public Link $selfLink) {}
}
