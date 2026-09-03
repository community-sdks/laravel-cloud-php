<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Caches;

use CommunitySDKs\LaravelCloud\Enums\Caches\CacheEvictionPolicy;

/** Optional cache updates; null fields are omitted. */ final readonly class UpdateCacheRequest
{
    public function __construct(public ?string $name = null, public ?string $size = null, public ?bool $autoUpgradeEnabled = null, public ?bool $isPublic = null, public ?CacheEvictionPolicy $evictionPolicy = null, public ?bool $usesHibernation = null, public int|string|null $hibernationTimeout = null) {} /** @return array<string,mixed> */ public function toArray(): array
    {
        return array_filter(['name' => $this->name,'size' => $this->size,'auto_upgrade_enabled' => $this->autoUpgradeEnabled,'is_public' => $this->isPublic,'eviction_policy' => $this->evictionPolicy?->value,'uses_hibernation' => $this->usesHibernation,'hibernation_timeout' => $this->hibernationTimeout], static fn(mixed $v): bool => null !== $v);
    }
}
