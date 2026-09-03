<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Commands;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Resources\Commands\CommandResource;

/** Paginated command response. */ final readonly class ListCommandsResponse
{/** @param list<CommandResource> $data */ public function __construct(public array $data, public PaginationLinks $links, public PaginationMeta $meta) {}
}
