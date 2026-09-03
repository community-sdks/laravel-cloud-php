<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\Commands;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\CreateCommandRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\GetCommandRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\ListCommandsRequest;
use CommunitySDKs\LaravelCloud\Enums\Commands\CommandInclude;
use CommunitySDKs\LaravelCloud\Enums\Commands\CommandStatus;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** Verifies listing, running, and retrieving commands. */
final class CommandsServiceTest extends TestCase
{
    public function test_all_command_endpoints_use_the_documented_contract(): void
    {
        $command = '{"type":"commands","id":"cmd-1","attributes":{"command":"php artisan about","output":"Laravel","status":"command.success","exit_code":0,"failure_reason":null,"created_at":"2026-01-01T00:00:00Z","started_at":"2026-01-01T00:00:01Z","finished_at":"2026-01-01T00:00:02Z"},"relationships":{"environment":{"data":{"type":"environments","id":"env-1"}},"deployment":{"data":null},"initiator":{"data":{"type":"users","id":"user-1"}}},"links":{"self":{"href":"https://mock.example/api/commands/cmd-1"}}}';
        $page = '{"data":[' . $command . '],"links":{"first":null,"last":null,"prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[],"path":"/api/commands","per_page":15,"to":1,"total":1}}';
        [$client, $handler] = $this->client([new Response(200, [], $page), new Response(200, [], '{"data":' . $command . '}'), new Response(200, [], '{"data":' . $command . '}')]);

        $listed = $client->commands()->list('env/1', new ListCommandsRequest('command.success', 'artisan', [CommandInclude::Environment]));
        self::assertNotNull($listed->data[0]->attributes);
        self::assertSame(CommandStatus::Success, $listed->data[0]->attributes->status);
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertStringContainsString('environments/env%2F1/commands?', (string) $sent->getUri());

        $client->commands()->run('env/1', new CreateCommandRequest('php artisan about'));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('POST', $sent->getMethod());
        self::assertJsonStringEqualsJsonString('{"command":"php artisan about"}', (string) $sent->getBody());

        $result = $client->commands()->get('cmd/1', new GetCommandRequest([CommandInclude::Initiator]));
        self::assertNotNull($result->data->attributes);
        self::assertSame(0, $result->data->attributes->exitCode);
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/commands/cmd%2F1?include=initiator', (string) $sent->getUri());
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
