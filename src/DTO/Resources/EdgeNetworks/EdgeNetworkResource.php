<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\EdgeNetworks;

use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\TenancyType;
use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\ZoneStatus;
use DateTimeImmutable;

/** Available edge network. */ final readonly class EdgeNetworkResource
{
    public function __construct(public string $id, public string $name, public string $domain, public TenancyType $tenancyType, public ZoneStatus $status, public ?DateTimeImmutable $createdAt) {}
}
