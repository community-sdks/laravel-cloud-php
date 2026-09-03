<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\AddEnvironmentVariablesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\AttachEnvironmentSecretsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\CreateEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\DeleteEnvironmentVariablesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentMetricsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\GetEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\ListEnvironmentLogsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\ListEnvironmentsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\PurgeEdgeCacheRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\StartEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\UpdateEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\UpdateVanityDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\DeploymentResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\EnvironmentMetricsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\EnvironmentResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\ListEnvironmentLogsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\ListEnvironmentsResponse;
use CommunitySDKs\LaravelCloud\Exceptions\ApiException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\RateLimitException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Environments\EnvironmentsHydrator;
use Throwable;

/** Manages environment configuration, lifecycle, logs, metrics, variables, and secrets.
 * All methods may throw AuthorizationException (403), NotFoundException (404),
 * TransportException, or ApiException. Mutations and queries documented with
 * validation additionally throw ValidationException (422).
 */
final class EnvironmentsService extends AbstractService
{
    public function __construct(ApiTransport $transport, private readonly EnvironmentsHydrator $hydrator = new EnvironmentsHydrator())
    {
        parent::__construct($transport);
    }
    /** List an application's environments using GET /applications/{application}/environments. */
    public function list(string $application, ?ListEnvironmentsRequest $request = null): ListEnvironmentsResponse
    {
        $payload = $this->transport->get('applications/' . rawurlencode($application) . '/environments', $request?->toQuery() ?? []);
        return $this->hydrate(fn() => $this->hydrator->list($payload), 'list-environments');
    }
    /** Create an environment using POST /applications/{application}/environments (201). @throws ValidationException */
    public function create(string $application, CreateEnvironmentRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('applications/' . rawurlencode($application) . '/environments', $request->toArray()), 'create-environment');
    }
    /** Retrieve one environment and optional relationships using GET /environments/{environment}. */
    public function get(string $environment, ?GetEnvironmentRequest $request = null): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->get('environments/' . rawurlencode($environment), $request?->toQuery() ?? []), 'get-environment');
    }
    /** Update optional environment configuration using PATCH /environments/{environment}. @throws ValidationException */
    public function update(string $environment, UpdateEnvironmentRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->patch('environments/' . rawurlencode($environment), $request->toArray()), 'update-environment');
    }
    /** Start an environment and return its deployment. @throws ValidationException */
    public function start(string $environment, ?StartEnvironmentRequest $request = null): DeploymentResponse
    {
        $payload = $this->transport->post('environments/' . rawurlencode($environment) . '/start', $request?->toArray());
        return $this->hydrate(fn() => $this->hydrator->deployment($payload), 'start-environment');
    }
    /** Stop an environment and cancel any deployment in progress. @throws ValidationException */
    public function stop(string $environment): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/stop'), 'stop-environment');
    }
    /** Purge all edge cache or one path, prefix, or tag subset. @throws ValidationException */
    public function purgeEdgeCache(string $environment, ?PurgeEdgeCacheRequest $request = null): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/purge-edge-cache', $request?->toArray()), 'purge-edge-cache');
    }
    /** Change the vanity domain; Laravel Cloud rate-limits this to once per 30 minutes. @throws ValidationException @throws RateLimitException */
    public function updateVanityDomain(string $environment, UpdateVanityDomainRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->put('environments/' . rawurlencode($environment) . '/vanity-domain', $request->toArray()), 'update-vanity-domain');
    }
    /** Add or update environment variables atomically. @throws ValidationException */
    public function addVariables(string $environment, AddEnvironmentVariablesRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/variables', $request->toArray()), 'add-environment-variables');
    }
    /** Delete named environment variables atomically. @throws ValidationException */
    public function deleteVariables(string $environment, DeleteEnvironmentVariablesRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/variables/delete', $request->toArray()), 'delete-environment-variables');
    }
    /** Attach up to 30 organization secret IDs to an environment. @throws ValidationException */
    public function attachSecrets(string $environment, AttachEnvironmentSecretsRequest $request): EnvironmentResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/secrets', $request->toArray()), 'attach-environment-secrets');
    }
    /** List cursor-paginated normalized logs for a required time range. @throws ValidationException */
    public function logs(string $environment, ListEnvironmentLogsRequest $request): ListEnvironmentLogsResponse
    {
        $payload = $this->transport->get('environments/' . rawurlencode($environment) . '/logs', $request->toQuery());
        return $this->hydrate(fn() => $this->hydrator->logs($payload), 'environment-logs');
    }
    /** Get CPU, memory, HTTP, replica, and worker metrics. @throws ValidationException */
    public function metrics(string $environment, ?GetEnvironmentMetricsRequest $request = null): EnvironmentMetricsResponse
    {
        $payload = $this->transport->get('environments/' . rawurlencode($environment) . '/metrics', $request?->toQuery() ?? []);
        return $this->hydrate(fn() => $this->hydrator->metrics($payload), 'environment-metrics');
    }
    /** Delete an environment; HTTP 204 has no response body. @throws ValidationException */
    public function delete(string $environment): void
    {
        $this->transport->delete('environments/' . rawurlencode($environment));
    }
    /** @param callable():array<string,mixed> $request */
    private function one(callable $request, string $operation): EnvironmentResponse
    {
        $payload = $request();
        return $this->hydrate(fn() => $this->hydrator->one($payload), $operation);
    }
    /**
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
     */
    private function hydrate(callable $callback, string $operation): mixed
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            throw new TransportException(sprintf('Laravel Cloud returned an invalid %s response.', $operation), 0, $e);
        }
    }
}
