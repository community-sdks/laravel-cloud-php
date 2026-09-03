<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Commands;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;

/** Command resource with native relationship IDs. */ final readonly class CommandResource
{
    public function __construct(public string $id, public ?CommandAttributes $attributes, public ?string $environmentId, public ?string $deploymentId, public ?string $initiatorId, public Link $selfLink) {}
}
