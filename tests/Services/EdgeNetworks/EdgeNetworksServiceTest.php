<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\EdgeNetworks;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\EdgeNetworks\ListEdgeNetworksRequest;
use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\TenancyType;
use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\ZoneStatus;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** Verifies the edge-network catalogue endpoint. */
final class EdgeNetworksServiceTest extends TestCase
{
    public function test_it_lists_and_hydrates_edge_networks(): void
    {
        $json = '{"data":[{"type":"edge-networks","id":"edge-1","attributes":{"name":"Default","domain":"example.com","tenancy_type":"shared","status":"available","created_at":null}}],"links":{"first":null,"last":null,"prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[],"path":"/api/edge-networks","per_page":15,"to":1,"total":1}}';
        $handler = new MockHandler([new Response(200, [], $json)]);
        $client = new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler]));

        $response = $client->edgeNetworks()->list(new ListEdgeNetworksRequest('Default', 'example.com', 'available'));

        self::assertSame(TenancyType::Shared, $response->data[0]->tenancyType);
        self::assertSame(ZoneStatus::Available, $response->data[0]->status);
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/edge-networks?filter%5Bname%5D=Default&filter%5Bdomain%5D=example.com&filter%5Bstatus%5D=available', (string) $sent->getUri());
    }
}
