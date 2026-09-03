<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;

/** One cache type and its supported configuration options. */ final readonly class AvailableCacheType
{/** @param list<CloudRegion> $regions
 * @param list<CacheTypeSize> $sizes */ public function __construct(public string $type, public string $label, public array $regions, public array $sizes, public bool $supportsAutoUpgrade) {}
}
