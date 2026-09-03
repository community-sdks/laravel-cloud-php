<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\Services\ApplicationsService;
use CommunitySDKs\LaravelCloud\Services\DomainsService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;

/** Verifies the public client exposes stable, reusable typed service objects. */
final class ClientTest extends TestCase
{
    public function test_it_lazily_creates_and_reuses_typed_services(): void
    {
        $client = new Client('test-token', httpClient: new GuzzleClient(['handler' => new MockHandler()]));

        self::assertInstanceOf(ApplicationsService::class, $client->applications());
        self::assertSame($client->applications(), $client->applications());
        self::assertInstanceOf(DomainsService::class, $client->domains());
        self::assertSame($client->domains(), $client->domains());
    }
}
