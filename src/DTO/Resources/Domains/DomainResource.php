<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;

/** Complete JSON:API domain resource. */
final readonly class DomainResource
{
    public function __construct(public string $id, public ?DomainAttributes $attributes, public ?string $environmentId, public Link $selfLink) {}
}
