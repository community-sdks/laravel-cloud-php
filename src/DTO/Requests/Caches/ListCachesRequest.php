<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Caches;

use CommunitySDKs\LaravelCloud\Enums\Caches\CacheInclude;

/** Cache filters and includes. */ final readonly class ListCachesRequest
{/** @param list<CacheInclude> $include */ public function __construct(public ?string $type = null, public ?string $region = null, public ?string $status = null, public array $include = []) {} /** @return array<string,string> */ public function toQuery(): array
{
    $q = [];
    foreach (['filter[type]' => $this->type,'filter[region]' => $this->region,'filter[status]' => $this->status] as $k => $v) {
        if (null !== $v) {
            $q[$k] = $v;
        }
    }if ([] !== $this->include) {
        $q['include'] = implode(',', array_map(static fn(CacheInclude $v): string => $v->value, $this->include));
    }return$q;
}
}
