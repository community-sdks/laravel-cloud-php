<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Caches;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheResource;

/** Paginated cache collection. */ final readonly class ListCachesResponse
{/** @param list<CacheResource> $data
 * @param list<EnvironmentResource> $included */ public function __construct(public array $data, public PaginationLinks $links, public PaginationMeta $meta, public array $included) {}
}
