<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\Environments;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\AddEnvironmentVariablesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\AttachEnvironmentSecretsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\CreateEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\DeleteEnvironmentVariablesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\EnvironmentVariableInput;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentMetricsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\ListEnvironmentLogsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\ListEnvironmentsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\PurgeEdgeCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\StartEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\UpdateEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\UpdateVanityDomainRequest;
use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentInclude;
use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentVariablesInsertMethod;
use CommunitySDKs\LaravelCloud\Enums\Environments\LogType;
use CommunitySDKs\LaravelCloud\Enums\Environments\MetricPeriod;
use CommunitySDKs\LaravelCloud\Exceptions\RateLimitException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use CommunitySDKs\LaravelCloud\Http\ResponseDecoder;
use DateTimeImmutable;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/** Exercises every documented environment endpoint at the HTTP boundary. */
final class EnvironmentsServiceTest extends TestCase
{
    public function test_list_create_get_update_start_stop_and_delete(): void
    {
        [$client,$handler] = $this->client([new Response(200, [], $this->listFixture()),new Response(201, [], $this->fixture('environment.json')),new Response(200, [], $this->fixture('environment.json')),new Response(200, [], $this->fixture('environment.json')),new Response(200, [], $this->fixture('deployment.json')),new Response(200, [], $this->fixture('environment.json')),new Response(204)]);
        $list = $client->environments()->list('app/1', new ListEnvironmentsRequest(name: 'Production', include: [EnvironmentInclude::Application]));
        self::assertSame('env-1', $list->data[0]->id);
        self::assertStringContainsString('applications/app%2F1/environments', (string) $this->lastRequest($handler)->getUri());
        $client->environments()->create('app-1', new CreateEnvironmentRequest('main', 'Production'));
        self::assertSame('POST', $this->lastRequest($handler)->getMethod());
        $client->environments()->get('env/1', new GetEnvironmentRequest([EnvironmentInclude::Deployments]));
        self::assertStringContainsString('include=deployments', (string) $this->lastRequest($handler)->getUri());
        $client->environments()->update('env-1', new UpdateEnvironmentRequest(name: 'Staging', timeout: 30));
        self::assertJsonStringEqualsJsonString('{"name":"Staging","timeout":30}', (string) $this->lastRequest($handler)->getBody());
        $deployment = $client->environments()->start('env-1', new StartEnvironmentRequest(true));
        self::assertSame('dep-1', $deployment->data->id);
        $client->environments()->stop('env-1');
        self::assertStringEndsWith('/stop', (string) $this->lastRequest($handler)->getUri());
        $client->environments()->delete('env-1');
        self::assertSame('DELETE', $this->lastRequest($handler)->getMethod());
    }
    public function test_cache_vanity_variables_and_secrets_mutations(): void
    {
        [$client,$handler] = $this->client(array_fill(0, 5, new Response(200, [], $this->fixture('environment.json'))));
        $client->environments()->purgeEdgeCache('env-1', new PurgeEdgeCacheRequest(prefix: '/blog/'));
        self::assertJsonStringEqualsJsonString('{"prefix":"/blog/"}', (string) $this->lastRequest($handler)->getBody());
        $client->environments()->updateVanityDomain('env-1', new UpdateVanityDomainRequest('example.test'));
        self::assertSame('PUT', $this->lastRequest($handler)->getMethod());
        $client->environments()->addVariables('env-1', new AddEnvironmentVariablesRequest(EnvironmentVariablesInsertMethod::Set, [new EnvironmentVariableInput('APP_ENV', 'production')]));
        self::assertStringContainsString('APP_ENV', (string) $this->lastRequest($handler)->getBody());
        $client->environments()->deleteVariables('env-1', new DeleteEnvironmentVariablesRequest(['OLD_KEY']));
        self::assertStringEndsWith('/variables/delete', (string) $this->lastRequest($handler)->getUri());
        $client->environments()->attachSecrets('env-1', new AttachEnvironmentSecretsRequest(['secret-1']));
        self::assertJsonStringEqualsJsonString('{"secrets":["secret-1"]}', (string) $this->lastRequest($handler)->getBody());
    }
    public function test_logs_and_metrics_are_typed(): void
    {
        [$client,$handler] = $this->client([new Response(200, [], $this->fixture('environment-logs.json')),new Response(200, [], $this->fixture('environment-metrics.json'))]);
        $logs = $client->environments()->logs('env-1', new ListEnvironmentLogsRequest(new DateTimeImmutable('2026-09-03T09:00:00Z'), new DateTimeImmutable('2026-09-03T10:00:00Z'), type: LogType::Access, instances: ['instance-1']));
        self::assertSame('GET /', $logs->data[0]->message);
        self::assertStringContainsString('instances%5B%5D%5B0%5D=instance-1', (string) $handler->getLastRequest()?->getUri());
        $metrics = $client->environments()->metrics('env-1', new GetEnvironmentMetricsRequest(MetricPeriod::SixHours));
        self::assertSame(1.5, $metrics->cpuUsage->average[0]);
        self::assertStringEndsWith('period=6h', (string) $handler->getLastRequest()?->getUri());
    }
    public function test_documented_validation_and_rate_limit_errors_are_mapped(): void
    {
        [$invalid] = $this->client([new Response(422, [], '{"message":"Invalid.","errors":{"name":["Invalid."]}}')]);
        try {
            $invalid->environments()->delete('env-1');
            self::fail();
        } catch (ValidationException) {
        }
        [$limited] = $this->client([new Response(429, [], '{"message":"Wait."}')]);
        $this->expectException(RateLimitException::class);
        $limited->environments()->updateVanityDomain('env-1', new UpdateVanityDomainRequest('example.test'));
    }
    /**
     * @param list<Response> $responses
     *
     * @return array{Client, MockHandler}
     */
    private function client(array $responses): array
    {
        $handler = new MockHandler($responses);
        return[new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler])),$handler];
    }
    private function fixture(string $name): string
    {
        $v = file_get_contents(__DIR__ . '/../../Fixtures/' . $name);
        self::assertIsString($v);
        return$v;
    }

    private function lastRequest(MockHandler $handler): RequestInterface
    {
        $request = $handler->getLastRequest();
        self::assertNotNull($request);
        return $request;
    }
    private function listFixture(): string
    {
        $v = (new ResponseDecoder())->decode($this->fixture('environment.json'));
        $v['data'] = [$v['data']];
        $v['links'] = ['first' => 'one','last' => 'one','prev' => null,'next' => null];
        $v['meta'] = ['current_page' => 1,'from' => 1,'last_page' => 1,'links' => [],'path' => 'environments','per_page' => 15,'to' => 1,'total' => 1];
        return json_encode($v, JSON_THROW_ON_ERROR);
    }
}
