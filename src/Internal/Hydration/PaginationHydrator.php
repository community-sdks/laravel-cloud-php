<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginatorLink;

/** Hydrates shared Laravel paginator structures. */ final class PaginationHydrator
{/** @param array<string,mixed> $p
 * @return array{PaginationLinks,PaginationMeta} */ public function hydrate(array $p): array
{
    $l = DataReader::object($p['links'] ?? null, 'links');
    $m = DataReader::object($p['meta'] ?? null, 'meta');
    $pl = array_map(static function (mixed $v): PaginatorLink {
        $x = DataReader::object($v, 'meta.links[]');
        return new PaginatorLink(DataReader::nullableString($x['url'] ?? null, 'url'), DataReader::string($x['label'] ?? null, 'label'), DataReader::bool($x['active'] ?? null, 'active'));
    }, DataReader::list($m['links'] ?? null, 'meta.links'));
    return[new PaginationLinks(DataReader::nullableString($l['first'] ?? null, 'first'), DataReader::nullableString($l['last'] ?? null, 'last'), DataReader::nullableString($l['prev'] ?? null, 'prev'), DataReader::nullableString($l['next'] ?? null, 'next')),new PaginationMeta(DataReader::int($m['current_page'] ?? null, 'current_page'), DataReader::nullableInt($m['from'] ?? null, 'from'), DataReader::int($m['last_page'] ?? null, 'last_page'), $pl, DataReader::nullableString($m['path'] ?? null, 'path'), DataReader::int($m['per_page'] ?? null, 'per_page'), DataReader::nullableInt($m['to'] ?? null, 'to'), DataReader::int($m['total'] ?? null, 'total'))];
}
}
