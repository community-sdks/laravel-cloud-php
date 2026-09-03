<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Caches;

use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\AvailableCacheType;

/** Runtime cache configuration catalogue. */ final readonly class ListCacheTypesResponse
{/** @param list<AvailableCacheType> $data */ public function __construct(public array $data) {}
}
