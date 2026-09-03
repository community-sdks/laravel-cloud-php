<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\Domains;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\CreateDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\GetDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\ListDomainsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\UpdateDomainRequest;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainActionRequired;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainCloudflareStrategy;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainInclude;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainRedirect;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainStatus;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainVerificationMethod;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** Verifies paths, payloads, hydration, and errors for all domain endpoints. */
final class DomainsServiceTest extends TestCase
{
    public function test_it_lists_domains_with_documented_filters(): void
    {
        [$client, $handler] = $this->client([new Response(200, [], $this->fixture('list-domains.json'))]);
        $response = $client->domains()->list('env/1', new ListDomainsRequest('example.com', 'verified', 'pending', 'verified', [DomainInclude::Environment]));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('GET', $sent->getMethod());
        self::assertSame('https://mock.example/api/environments/env%2F1/domains?filter%5Bname%5D=example.com&filter%5Bhostname_status%5D=verified&filter%5Bssl_status%5D=pending&filter%5Borigin_status%5D=verified&include=environment', (string) $sent->getUri());
        self::assertSame('domain-1', $response->data[0]->id);
        self::assertSame(DomainStatus::Verified, $response->data[0]->attributes?->hostnameStatus);
        self::assertSame(1, $response->meta->total);
    }

    public function test_it_creates_and_fully_hydrates_a_domain(): void
    {
        [$client, $handler] = $this->client([new Response(200, [], $this->fixture('domain.json'))]);
        $response = $client->domains()->create('env-1', new CreateDomainRequest('example.com', DomainRedirect::RootToWww, true, false, DomainCloudflareStrategy::Dns, DomainVerificationMethod::PreVerification));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('POST', $sent->getMethod());
        self::assertJsonStringEqualsJsonString('{"name":"example.com","www_redirect":"root_to_www","wildcard_enabled":true,"allow_downtime":false,"cloudflare_strategy":"dns","verification_method":"pre_verification"}', (string) $sent->getBody());
        self::assertSame('env-1', $response->data->environmentId);
        self::assertSame(DomainActionRequired::AddDnsRecords, $response->data->attributes?->actionRequired);
        self::assertSame('CNAME', $response->data->attributes->dnsRecords->ssl[0]->type);
        self::assertSame(DomainStatus::Pending, $response->data->attributes->www?->sslStatus);
    }

    public function test_get_update_verify_and_delete_use_the_documented_wire_contracts(): void
    {
        [$client, $handler] = $this->client([
            new Response(200, [], $this->fixture('domain.json')),
            new Response(200, [], $this->fixture('domain.json')),
            new Response(200, [], $this->fixture('domain.json')),
            new Response(204),
        ]);
        $client->domains()->get('domain/1', new GetDomainRequest(true, [DomainInclude::Environment]));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/domains/domain%2F1?verify=1&include=environment', (string) $sent->getUri());
        $client->domains()->update('domain/1', new UpdateDomainRequest(DomainVerificationMethod::RealTime));
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('PATCH', $sent->getMethod());
        self::assertJsonStringEqualsJsonString('{"verification_method":"real_time"}', (string) $sent->getBody());
        $client->domains()->verify('domain/1');
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('https://mock.example/api/domains/domain%2F1/verify', (string) $sent->getUri());
        $client->domains()->delete('domain/1');
        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('DELETE', $sent->getMethod());
        self::assertSame('https://mock.example/api/domains/domain%2F1', (string) $sent->getUri());
    }

    public function test_domain_endpoints_map_documented_errors(): void
    {
        [$forbidden] = $this->client([new Response(403, [], '{"message":"Forbidden."}')]);
        try {
            $forbidden->domains()->get('domain-1');
            self::fail('Expected authorization error.');
        } catch (AuthorizationException) {
        }
        [$missing] = $this->client([new Response(404, [], '{"message":"Missing."}')]);
        try {
            $missing->domains()->delete('domain-1');
            self::fail('Expected not-found error.');
        } catch (NotFoundException) {
        }
        [$invalid] = $this->client([new Response(422, [], '{"message":"Invalid.","errors":{"name":["Invalid."]}}')]);
        $this->expectException(ValidationException::class);
        $invalid->domains()->create('env-1', new CreateDomainRequest('example.com'));
    }

    /**
     * @param list<Response> $responses
     *
     * @return array{Client, MockHandler}
     */
    private function client(array $responses): array
    {
        $handler = new MockHandler($responses);
        return [new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler])), $handler];
    }

    private function fixture(string $name): string
    {
        $fixture = file_get_contents(__DIR__ . '/../../Fixtures/' . $name);
        self::assertIsString($fixture);
        return $fixture;
    }
}
