<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Http;

use CommunitySDKs\LaravelCloud\Http\ClientConfiguration;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** Verifies connection settings are normalized and rejected when unsafe. */
final class ClientConfigurationTest extends TestCase
{
    public function test_it_normalizes_the_base_uri(): void
    {
        $configuration = new ClientConfiguration('token', 'https://example.test/api');

        self::assertSame('https://example.test/api/', $configuration->normalizedBaseUri());
    }

    public function test_it_rejects_an_empty_token(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ClientConfiguration('  ');
    }
}
