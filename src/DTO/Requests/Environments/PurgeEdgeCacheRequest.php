<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Optional single selector for purging an environment's edge cache. */
final readonly class PurgeEdgeCacheRequest
{
    public function __construct(public ?string $path = null, public ?string $prefix = null, public ?string $tag = null)
    {
        $set = array_filter([$path,$prefix,$tag], static fn(?string $v): bool => null !== $v);
        if (count($set) > 1) {
            throw new InvalidArgumentException('Only one of path, prefix, or tag may be supplied.');
        }if (null !== $path && strlen($path) > 2048 || null !== $prefix && strlen($prefix) > 2048 || null !== $tag && strlen($tag) > 1024) {
            throw new InvalidArgumentException('Edge cache selector exceeds its documented length.');
        }
    }
    /** @return array<string,string> */
    public function toArray(): array
    {
        $v = [];
        foreach (['path' => $this->path,'prefix' => $this->prefix,'tag' => $this->tag] as $k => $x) {
            if (null !== $x) {
                $v[$k] = $x;
            }
        }return $v;
    }
}
