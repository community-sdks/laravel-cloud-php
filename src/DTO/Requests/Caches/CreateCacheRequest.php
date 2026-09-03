<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Caches;

use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheEvictionPolicy;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheType;
use InvalidArgumentException;

/** Cache creation request. */ final readonly class CreateCacheRequest
{
    public function __construct(public CacheType $type, public string $name, public CloudRegion $region, public string $size, public bool $autoUpgradeEnabled, public bool $isPublic, public ?string $clusterId = null, public ?CacheEvictionPolicy $evictionPolicy = null, public bool $usesHibernation = false, public int|string|null $hibernationTimeout = null)
    {
        if (1 !== preg_match('/^[a-z0-9_-]{3,40}$/', $name)) {
            throw new InvalidArgumentException('Invalid cache name.');
        }
    } /** @return array<string,mixed> */ public function toArray(): array
    {
        return['type' => $this->type->value,'name' => $this->name,'region' => $this->region->value,'size' => $this->size,'auto_upgrade_enabled' => $this->autoUpgradeEnabled,'is_public' => $this->isPublic,'cluster_id' => $this->clusterId,'eviction_policy' => $this->evictionPolicy?->value,'uses_hibernation' => $this->usesHibernation,'hibernation_timeout' => $this->hibernationTimeout];
    }
}
