<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Common;

/** One generated page link in Laravel's paginator metadata. */
final readonly class PaginatorLink
{
    public function __construct(
        public ?string $url,
        public string $label,
        public bool $active,
    ) {}
}
