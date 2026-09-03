# Caches Service

Create, inspect, update, monitor, and delete managed caches. Access it with `$cloud->caches()`.

## Endpoints

| SDK method | HTTP endpoint | Typed result |
| --- | --- | --- |
| `list(?ListCachesRequest $request)` | `GET /caches` | `ListCachesResponse` |
| `create(CreateCacheRequest $request)` | `POST /caches` | `CacheResponse` |
| `get(string $id)` | `GET /caches/{cache}` | `CacheResponse` |
| `update(string $id, UpdateCacheRequest $request)` | `PATCH /caches/{cache}` | `CacheResponse` |
| `metrics(string $id, ?GetEnvironmentMetricsRequest $request)` | `GET /caches/{cache}/metrics` | `CacheMetricsResponse` |
| `types()` | `GET /caches/types` | `ListCacheTypesResponse` |
| `delete(string $id)` | `DELETE /caches/{cache}` | `void` |

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\CreateCacheRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheType;

$catalogue = $cloud->caches()->types();
$cache = $cloud->caches()->create(new CreateCacheRequest(
    type: CacheType::LaravelValkey,
    name: 'primary_cache',
    region: CloudRegion::EuropeWest1,
    size: $catalogue->data[0]->sizes[0]->value,
    autoUpgradeEnabled: true,
    isPublic: false,
));
```

Cache sizes remain strings: call `types()` to discover the sizes currently supported for each type and region.

[Back to the service index](README.md)
