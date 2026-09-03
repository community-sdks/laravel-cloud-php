<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Common;

/** Top-level navigation links for a paginated response. */
final readonly class PaginationLinks
{
    public function __construct(
        public ?string $first,
        public ?string $last,
        public ?string $prev,
        public ?string $next,
    ) {}
}
