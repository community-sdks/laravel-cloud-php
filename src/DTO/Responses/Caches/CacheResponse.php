<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Caches;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheResource;

/** Single cache document. */ final readonly class CacheResponse
{/** @param list<EnvironmentResource> $included */ public function __construct(public CacheResource $data, public array $included) {}
}
