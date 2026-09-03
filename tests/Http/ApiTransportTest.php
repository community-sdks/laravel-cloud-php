<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Http;

use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\ServerException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Http\ClientConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/** Verifies request construction and SDK-specific transport error behavior. */
final class ApiTransportTest extends TestCase
{
    public function test_it_sends_configured_headers_and_decodes_json(): void
    {
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/vnd.api+json'], '{"data":{"id":"123"}}'),
        ]);
        $transport = new ApiTransport(
            new ClientConfiguration('secret', 'https://example.test/api/', userAgent: 'sdk-test/1.0'),
            new Client(['handler' => $handler]),
        );

        $result = $transport->get('/resource', ['page' => 2]);

        self::assertSame(['data' => ['id' => '123']], $result);
        $request = $handler->getLastRequest();
        self::assertNotNull($request);
        self::assertSame('https://example.test/api/resource?page=2', (string) $request->getUri());
        self::assertSame('Bearer secret', $request->getHeaderLine('Authorization'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame('sdk-test/1.0', $request->getHeaderLine('User-Agent'));
    }

    public function test_it_maps_documented_http_categories_to_sdk_exceptions(): void
    {
        $transport = $this->transportWith(new Response(
            403,
            ['Content-Type' => 'application/vnd.api+json'],
            '{"errors":[{"detail":"Forbidden operation"}]}',
        ));

        try {
            $transport->post('/resource');
            self::fail('Expected an authorization exception.');
        } catch (AuthorizationException $exception) {
            self::assertSame(403, $exception->getStatusCode());
            self::assertSame('Forbidden operation', $exception->getApiMessage());
            self::assertStringContainsString('errors', $exception->getResponseBody());
        }
    }

    public function test_it_maps_server_errors(): void
    {
        $this->expectException(ServerException::class);
        $this->transportWith(new Response(503))->get('/resource');
    }

    public function test_it_wraps_network_failures(): void
    {
        $failure = new ConnectException('Connection failed', new Request('GET', '/resource'));
        $this->expectException(TransportException::class);
        $this->transportWith($failure)->get('/resource');
    }

    public function test_it_rejects_absolute_endpoint_paths(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->transportWith(new Response(200))->get('https://untrusted.example/resource');
    }

    private function transportWith(ResponseInterface|ConnectException $result): ApiTransport
    {
        return new ApiTransport(
            new ClientConfiguration('secret', 'https://example.test/api/'),
            new Client(['handler' => new MockHandler([$result])]),
        );
    }
}
