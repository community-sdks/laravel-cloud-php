<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\EdgeNetworks;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Resources\EdgeNetworks\EdgeNetworkResource;

/** Paginated edge-network catalogue. */ final readonly class ListEdgeNetworksResponse
{/** @param list<EdgeNetworkResource> $data */ public function __construct(public array $data, public PaginationLinks $links, public PaginationMeta $meta) {}
}
