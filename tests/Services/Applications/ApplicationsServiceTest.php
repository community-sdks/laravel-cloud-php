<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\Services\Applications;

use CommunitySDKs\LaravelCloud\Client;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\CreateApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\GetApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\ListApplicationsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UpdateApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UploadApplicationAvatarRequest;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentVariables;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryResource;
use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Applications\DeploymentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\EnvironmentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\NodeVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\PhpMajorVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitLevel;
use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitPerMinute;
use CommunitySDKs\LaravelCloud\Enums\Applications\SourceControlProviderType;
use CommunitySDKs\LaravelCloud\Exceptions\AuthenticationException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** Verifies the implemented Laravel Cloud application endpoint contracts. */
final class ApplicationsServiceTest extends TestCase
{
    public function test_it_lists_and_hydrates_applications_with_included_resources(): void
    {
        $handler = new MockHandler([new Response(200, [], $this->fixture())]);
        $client = new Client(
            token: 'secret',
            baseUri: 'https://mock.example/api',
            httpClient: new GuzzleClient(['handler' => $handler]),
        );

        $response = $client->applications()->list(new ListApplicationsRequest(
            name: 'Shop',
            region: 'eu-central-1',
            slug: 'shop',
            include: [ApplicationInclude::Organization, ApplicationInclude::Environments],
        ));

        $request = $handler->getLastRequest();
        self::assertNotNull($request);
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            'https://mock.example/api/applications?filter%5Bname%5D=Shop&filter%5Bregion%5D=eu-central-1&filter%5Bslug%5D=shop&include=organization%2Cenvironments',
            (string) $request->getUri(),
        );
        self::assertSame('Bearer secret', $request->getHeaderLine('Authorization'));

        self::assertCount(1, $response->data);
        $application = $response->data[0];
        $attributes = $application->attributes;
        self::assertNotNull($attributes);
        self::assertSame('app-1', $application->id);
        self::assertSame(CloudRegion::EuropeCentral1, $attributes->region);
        self::assertNull($attributes->rootDirectory);
        self::assertSame('2026-08-20', $attributes->createdAt?->format('Y-m-d'));
        $relationships = $application->relationships;
        self::assertNotNull($relationships);
        self::assertSame('env-1', $relationships->defaultEnvironmentId);
        self::assertSame(['env-1'], $relationships->environmentIds);
        self::assertSame(['dep-1'], $relationships->deploymentIds);

        self::assertCount(4, $response->included);
        self::assertInstanceOf(RepositoryResource::class, $response->included[0]);
        self::assertInstanceOf(OrganizationResource::class, $response->included[1]);
        self::assertInstanceOf(EnvironmentResource::class, $response->included[2]);
        self::assertInstanceOf(DeploymentResource::class, $response->included[3]);

        $environment = $response->included[2];
        $environmentAttributes = $environment->attributes;
        self::assertNotNull($environmentAttributes);
        self::assertSame(EnvironmentStatus::Running, $environmentAttributes->status);
        self::assertSame(PhpMajorVersion::Php85, $environmentAttributes->phpMajorVersion);
        self::assertSame(NodeVersion::Node24, $environmentAttributes->nodeVersion);
        self::assertSame(RateLimitLevel::Throttle, $environmentAttributes->networkSettings->firewall->rateLimit->level);
        self::assertSame(RateLimitPerMinute::ThreeHundred, $environmentAttributes->networkSettings->firewall->rateLimit->perMinute);
        self::assertInstanceOf(EnvironmentVariables::class, $environmentAttributes->environmentVariables);

        $deployment = $response->included[3];
        $deploymentAttributes = $deployment->attributes;
        self::assertNotNull($deploymentAttributes);
        self::assertSame(DeploymentStatus::DeploymentSucceeded, $deploymentAttributes->status);
        self::assertNull($deploymentAttributes->commitAuthor);
        self::assertSame(1, $response->meta->total);
        self::assertNull($response->links->next);
    }

    public function test_it_maps_the_documented_authentication_error(): void
    {
        $client = $this->clientWith(new Response(401, [], '{"message":"Unauthenticated."}'));

        $this->expectException(AuthenticationException::class);
        $client->applications()->list();
    }

    public function test_it_maps_the_documented_authorization_error(): void
    {
        $client = $this->clientWith(new Response(403, [], '{"message":"Forbidden."}'));

        $this->expectException(AuthorizationException::class);
        $client->applications()->list();
    }

    public function test_it_rejects_an_invalid_success_payload(): void
    {
        $client = $this->clientWith(new Response(200, [], '{"data":"invalid","links":{},"meta":{}}'));

        $this->expectException(TransportException::class);
        $client->applications()->list();
    }

    public function test_it_gets_and_hydrates_one_application(): void
    {
        $handler = new MockHandler([new Response(200, [], $this->createFixture())]);
        $client = new Client(
            token: 'secret',
            baseUri: 'https://mock.example/api',
            httpClient: new GuzzleClient(['handler' => $handler]),
        );

        $response = $client->applications()->get('app/2', new GetApplicationRequest([
            ApplicationInclude::Organization,
            ApplicationInclude::DefaultEnvironment,
        ]));

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('GET', $sent->getMethod());
        self::assertSame(
            'https://mock.example/api/applications/app%2F2?include=organization%2CdefaultEnvironment',
            (string) $sent->getUri(),
        );
        self::assertSame('Bearer secret', $sent->getHeaderLine('Authorization'));
        self::assertSame('app-2', $response->data->id);
        self::assertCount(1, $response->included);
        self::assertInstanceOf(RepositoryResource::class, $response->included[0]);
    }

    public function test_get_maps_the_documented_authorization_error(): void
    {
        $client = $this->clientWith(new Response(403, [], '{"message":"Forbidden."}'));

        $this->expectException(AuthorizationException::class);
        $client->applications()->get('app-2');
    }

    public function test_get_maps_the_documented_not_found_error(): void
    {
        $client = $this->clientWith(new Response(404, [], '{"message":"Application not found."}'));

        $this->expectException(NotFoundException::class);
        $client->applications()->get('missing');
    }

    public function test_get_rejects_an_invalid_success_payload(): void
    {
        $client = $this->clientWith(new Response(200, [], '{"data":null}'));

        $this->expectException(TransportException::class);
        $client->applications()->get('app-2');
    }

    public function test_it_deletes_an_application_with_no_response_body(): void
    {
        $handler = new MockHandler([new Response(204)]);
        $client = new Client(
            token: 'secret',
            baseUri: 'https://mock.example/api',
            httpClient: new GuzzleClient(['handler' => $handler]),
        );

        $client->applications()->delete('app/2');

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('DELETE', $sent->getMethod());
        self::assertSame('https://mock.example/api/applications/app%2F2', (string) $sent->getUri());
        self::assertSame('Bearer secret', $sent->getHeaderLine('Authorization'));
        self::assertSame('', (string) $sent->getBody());
    }

    public function test_delete_maps_the_documented_authorization_error(): void
    {
        $client = $this->clientWith(new Response(403, [], '{"message":"Forbidden."}'));

        $this->expectException(AuthorizationException::class);
        $client->applications()->delete('app-2');
    }

    public function test_delete_maps_the_documented_not_found_error(): void
    {
        $client = $this->clientWith(new Response(404, [], '{"message":"Application not found."}'));

        $this->expectException(NotFoundException::class);
        $client->applications()->delete('missing');
    }

    public function test_it_updates_and_hydrates_an_application(): void
    {
        $handler = new MockHandler([new Response(200, [], $this->createFixture())]);
        $client = new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler]));

        $response = $client->applications()->update('app/2', new UpdateApplicationRequest(
            sourceControlProviderType: SourceControlProviderType::GitLab,
            name: 'Renamed App',
            slug: 'renamed-app',
            defaultEnvironmentId: 'env-2',
            repository: 'acme/renamed-app',
            clearSlackChannel: true,
        ));

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('PATCH', $sent->getMethod());
        self::assertSame('https://mock.example/api/applications/app%2F2', (string) $sent->getUri());
        self::assertJsonStringEqualsJsonString(
            '{"source_control_provider_type":"gitlab","name":"Renamed App","slug":"renamed-app","default_environment_id":"env-2","repository":"acme/renamed-app","slack_channel":null}',
            (string) $sent->getBody(),
        );
        self::assertSame('app-2', $response->data->id);
    }

    public function test_update_maps_the_documented_validation_error(): void
    {
        $client = $this->clientWith(new Response(422, [], '{"message":"Invalid data.","errors":{"slug":["Invalid slug."]}}'));

        $this->expectException(ValidationException::class);
        $client->applications()->update('app-2', new UpdateApplicationRequest(slug: 'valid-slug'));
    }

    public function test_it_uploads_an_avatar_as_multipart_and_hydrates_the_application(): void
    {
        $handler = new MockHandler([new Response(200, [], $this->createFixture())]);
        $client = new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler]));

        $response = $client->applications()->uploadAvatar(
            'app/2',
            new UploadApplicationAvatarRequest("binary\0avatar", 'avatar.png'),
        );

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('POST', $sent->getMethod());
        self::assertSame('https://mock.example/api/applications/app%2F2/avatar', (string) $sent->getUri());
        self::assertStringStartsWith('multipart/form-data; boundary=', $sent->getHeaderLine('Content-Type'));
        self::assertStringContainsString('name="avatar"; filename="avatar.png"', (string) $sent->getBody());
        self::assertStringContainsString("binary\0avatar", (string) $sent->getBody());
        self::assertSame('app-2', $response->data->id);
    }

    public function test_upload_avatar_maps_the_documented_validation_error(): void
    {
        $client = $this->clientWith(new Response(422, [], '{"message":"Invalid image.","errors":{"avatar":["Invalid image."]}}'));

        $this->expectException(ValidationException::class);
        $client->applications()->uploadAvatar('app-2', new UploadApplicationAvatarRequest('image'));
    }

    public function test_it_deletes_an_application_avatar(): void
    {
        $handler = new MockHandler([new Response(204)]);
        $client = new Client('secret', 'https://mock.example/api', httpClient: new GuzzleClient(['handler' => $handler]));

        $client->applications()->deleteAvatar('app/2');

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('DELETE', $sent->getMethod());
        self::assertSame('https://mock.example/api/applications/app%2F2/avatar', (string) $sent->getUri());
    }

    public function test_delete_avatar_maps_the_documented_not_found_error(): void
    {
        $client = $this->clientWith(new Response(404, [], '{"message":"Application not found."}'));

        $this->expectException(NotFoundException::class);
        $client->applications()->deleteAvatar('missing');
    }

    public function test_it_creates_and_hydrates_an_application(): void
    {
        $handler = new MockHandler([new Response(201, [], $this->createFixture())]);
        $client = new Client(
            token: 'secret',
            baseUri: 'https://mock.example/api',
            httpClient: new GuzzleClient(['handler' => $handler]),
        );

        $response = $client->applications()->create(new CreateApplicationRequest(
            sourceControlProviderType: SourceControlProviderType::GitHub,
            repository: 'acme/customer-portal',
            name: 'Customer Portal',
            region: CloudRegion::EuropeWest1,
            rootDirectory: 'apps/portal',
            clusterId: null,
        ));

        $sent = $handler->getLastRequest();
        self::assertNotNull($sent);
        self::assertSame('POST', $sent->getMethod());
        self::assertSame('https://mock.example/api/applications', (string) $sent->getUri());
        self::assertSame('application/json', $sent->getHeaderLine('Content-Type'));
        self::assertJsonStringEqualsJsonString(
            '{"source_control_provider_type":"github","repository":"acme/customer-portal","name":"Customer Portal","region":"eu-west-1","root_directory":"apps/portal","cluster_id":null}',
            (string) $sent->getBody(),
        );
        self::assertSame('app-2', $response->data->id);
        self::assertSame('Customer Portal', $response->data->attributes?->name);
        self::assertNull($response->data->relationships?->defaultEnvironmentId);
        self::assertCount(1, $response->included);
        self::assertInstanceOf(RepositoryResource::class, $response->included[0]);
    }

    public function test_create_maps_the_documented_validation_error(): void
    {
        $client = $this->clientWith(new Response(422, [], '{"message":"Invalid data.","errors":{"name":["Invalid name."]}}'));

        try {
            $client->applications()->create(new CreateApplicationRequest(
                SourceControlProviderType::GitHub,
                'acme/repository',
                'Valid Name',
                CloudRegion::UsEast1,
            ));
            self::fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            self::assertSame(422, $exception->getStatusCode());
            self::assertSame(['name' => ['Invalid name.']], $exception->getErrors());
        }
    }

    private function clientWith(Response $response): Client
    {
        return new Client(
            token: 'secret',
            baseUri: 'https://mock.example/api/',
            httpClient: new GuzzleClient(['handler' => new MockHandler([$response])]),
        );
    }

    private function fixture(): string
    {
        $fixture = file_get_contents(__DIR__ . '/../../Fixtures/list-applications.json');
        self::assertIsString($fixture);

        return $fixture;
    }

    private function createFixture(): string
    {
        $fixture = file_get_contents(__DIR__ . '/../../Fixtures/create-application.json');
        self::assertIsString($fixture);

        return $fixture;
    }
}
