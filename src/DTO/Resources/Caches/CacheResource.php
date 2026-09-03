<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

/** JSON:API cache resource with environment string IDs. */ final readonly class CacheResource
{/** @param list<string>|null $environmentIds */ public function __construct(public string $id, public ?CacheAttributes $attributes, public ?array $environmentIds) {}
}
