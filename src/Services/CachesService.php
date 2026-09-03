<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\CreateCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\ListCachesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\UpdateCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentMetricsRequest;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\CacheMetricsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\CacheResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\ListCachesResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\ListCacheTypesResponse;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Caches\CachesHydrator;

/** Manages cache resources and their available configuration catalogue. */ final class CachesService extends AbstractService
{
    public function __construct(ApiTransport $t, private readonly CachesHydrator $h = new CachesHydrator())
    {
        parent::__construct($t);
    } /** List caches with filters and environment includes. */ public function list(?ListCachesRequest $r = null): ListCachesResponse
    {
        return $this->h->list($this->transport->get('caches', $r?->toQuery() ?? []));
    } /** Create a cache. */ public function create(CreateCacheRequest $r): CacheResponse
    {
        return $this->h->one($this->transport->post('caches', $r->toArray()));
    } /** Get a cache by string ID. */ public function get(string $id): CacheResponse
    {
        return $this->h->one($this->transport->get('caches/' . rawurlencode($id)));
    } /** Update a cache. */ public function update(string $id, UpdateCacheRequest $r): CacheResponse
    {
        return $this->h->one($this->transport->patch('caches/' . rawurlencode($id), $r->toArray()));
    } /** Get cache metrics. */ public function metrics(string $id, ?GetEnvironmentMetricsRequest $r = null): CacheMetricsResponse
    {
        return $this->h->metrics($this->transport->get('caches/' . rawurlencode($id) . '/metrics', $r?->toQuery() ?? []));
    } /** Discover cache types, regions, sizes, and auto-upgrade support. */ public function types(): ListCacheTypesResponse
    {
        return $this->h->types($this->transport->get('caches/types'));
    } /** Delete a cache; HTTP 204 has no body. */ public function delete(string $id): void
    {
        $this->transport->delete('caches/' . rawurlencode($id));
    }
}
