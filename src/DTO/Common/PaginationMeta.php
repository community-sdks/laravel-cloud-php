<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Common;

/** Page counts and generated links for a paginated Laravel Cloud response. */
final readonly class PaginationMeta
{
    /** @param list<PaginatorLink> $links */
    public function __construct(
        public int $currentPage,
        public ?int $from,
        public int $lastPage,
        public array $links,
        public ?string $path,
        public int $perPage,
        public ?int $to,
        public int $total,
    ) {}
}
