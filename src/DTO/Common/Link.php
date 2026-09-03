<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Common;

/** A JSON:API link object returned by Laravel Cloud. */
final readonly class Link
{
    /**
     * @param string|list<string>|null $hreflang
     * @param array<string, mixed>|null $meta Link metadata has no defined OpenAPI fields.
     */
    public function __construct(
        public string $href,
        public ?string $rel,
        public ?string $describedby,
        public ?string $title,
        public ?string $type,
        public string|array|null $hreflang,
        public ?array $meta,
    ) {}
}
