<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheStatus;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheType;
use DateTimeImmutable;

/** Complete cache attributes. */ final readonly class CacheAttributes
{
    public function __construct(public string $name, public CacheType $type, public CacheStatus $status, public CloudRegion $region, public string $size, public bool $autoUpgradeEnabled, public bool $isPublic, public bool $usesHibernation, public ?int $hibernationTimeout, public ?DateTimeImmutable $createdAt, public CacheConnection $connection) {}
}
