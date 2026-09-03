<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\Caches;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\CreateCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\ListCachesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Caches\UpdateCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentMetricsRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheEvictionPolicy;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheInclude;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheStatus;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheType;
use CommunitySDKs\LaravelCloud\Enums\Environments\MetricPeriod;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** Verifies every documented cache endpoint and its typed response. */
final class CachesServiceTest extends TestCase
{
    public function test_it_lists_creates_gets_updates_and_deletes_caches(): void
    {
        [$client, $handler] = $this->client([
            new Response(200, [], $this->paginated('[' . $this->cache() . ']')),
            new Response(200, [], '{"data":' . $this->cache() . '}'),
            new Response(200, [], '{"data":' . $this->cache() . '}'),
            new Response(200, [], '{"data":' . $this->cache() . '}'),
            new Response(204),
        ]);

        $listed = $client->caches()->list(new ListCachesRequest('laravel_valkey', 'eu-west-1', 'available', [CacheInclude::Environments]));
        self::assertSame(CacheStatus::Available, $listed->data[0]->attributes?->status);
        self::assertSame(1, $listed->meta->total);

        $created = $client->caches()->create(new CreateCacheRequest(CacheType::LaravelValkey, 'main_cache', CloudRegion::EuropeWest1, 'cache.t4g.small', true, false, evictionPolicy: CacheEvictionPolicy::AllKeysLru));
        self::assertSame('cache-1', $created->data->id);
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('POST', $sent->getMethod());
        self::assertStringContainsString('"name":"main_cache"', (string) $sent->getBody());

        $client->caches()->get('cache/1');
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/caches/cache%2F1', (string) $sent->getUri());

        $client->caches()->update('cache/1', new UpdateCacheRequest(name: 'new_cache', isPublic: true));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('PATCH', $sent->getMethod());
        self::assertJsonStringEqualsJsonString('{"name":"new_cache","is_public":true}', (string) $sent->getBody());

        $client->caches()->delete('cache/1');
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('DELETE', $sent->getMethod());
    }

    public function test_it_hydrates_types_and_metrics(): void
    {
        $types = '{"data":[{"type":"laravel_valkey","label":"Valkey","regions":["eu-west-1"],"sizes":[{"value":"cache.t4g.small","label":"Small"}],"supports_auto_upgrade":true}]}';
        $metrics = '{"data":{"hits_and_misses":{"labels":["hits"],"average":[4],"data":[{"x":"2026-01-01T00:00:00Z","y":[4]}]},"throughput":{"labels":["read"],"average":[2],"data":[{"x":"2026-01-01T00:00:00Z","y":[2]}]},"size":{"data":[{"x":"2026-01-01T00:00:00Z","y":128}],"total":128},"bandwidth_usage":{"data":[{"x":"2026-01-01T00:00:00Z","y":64}],"total":64}},"meta":{"period":"24h","available_periods":["6h","24h"]}}';
        [$client, $handler] = $this->client([new Response(200, [], $types), new Response(200, [], $metrics)]);

        $catalogue = $client->caches()->types();
        self::assertSame(CloudRegion::EuropeWest1, $catalogue->data[0]->regions[0]);
        self::assertTrue($catalogue->data[0]->supportsAutoUpgrade);

        $response = $client->caches()->metrics('cache/1', new GetEnvironmentMetricsRequest(MetricPeriod::TwentyFourHours));
        self::assertSame(4.0, $response->hitsAndMisses->average[0]);
        self::assertSame(128.0, $response->size->total);
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/caches/cache%2F1/metrics?period=24h', (string) $sent->getUri());
    }

    private function cache(): string
    {
        return '{"type":"caches","id":"cache-1","attributes":{"name":"main_cache","type":"laravel_valkey","status":"available","region":"eu-west-1","size":"cache.t4g.small","auto_upgrade_enabled":true,"is_public":false,"uses_hibernation":false,"hibernation_timeout":null,"created_at":"2026-01-01T00:00:00Z","connection":{"hostname":"cache.test","port":6379,"protocol":"redis","":{"username":"default","password":"secret"}}},"relationships":{"environments":{"data":[{"type":"environments","id":"env-1"}]}}}';
    }

    private function paginated(string $data): string
    {
        return '{"data":' . $data . ',"links":{"first":null,"last":null,"prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[],"path":"/api/caches","per_page":15,"to":1,"total":1}}';
    }

    /** @param list<Response> $responses
     * @return array{Client, MockHandler}
     */
    private function client(array $responses): array
    {
        $handler = new MockHandler($responses);
        return [new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler])), $handler];
    }
}
