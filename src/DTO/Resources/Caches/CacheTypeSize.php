<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

/** One runtime-discovered cache size option. */ final readonly class CacheTypeSize
{
    public function __construct(public string $value, public string $label) {}
}
