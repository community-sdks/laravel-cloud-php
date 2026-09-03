<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\CreateDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\GetDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\ListDomainsRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\UpdateDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Responses\Domains\DomainResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Domains\ListDomainsResponse;
use CommunitySDKs\LaravelCloud\Exceptions\ApiException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Domains\DomainsHydrator;
use Throwable;

/** Manages custom domains and their DNS verification lifecycle. */
final class DomainsService extends AbstractService
{
    public function __construct(ApiTransport $transport, private readonly DomainsHydrator $hydrator = new DomainsHydrator())
    {
        parent::__construct($transport);
    }
    /** List an environment's domains with optional filters and includes.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws TransportException For transport or hydration failures. @throws ApiException For other API errors.
     */
    public function list(string $environment, ?ListDomainsRequest $request = null): ListDomainsResponse
    {
        $payload = $this->transport->get('environments/' . rawurlencode($environment) . '/domains', $request?->toQuery() ?? []);
        try {
            return $this->hydrator->hydrateList($payload);
        } catch (Throwable $e) {
            throw new TransportException('Laravel Cloud returned an invalid domains response.', 0, $e);
        }
    }
    /** Create a domain and return its DNS requirements.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws ValidationException For HTTP 422. @throws TransportException For transport or hydration failures.
     * @throws ApiException For other API errors.
     */
    public function create(string $environment, CreateDomainRequest $request): DomainResponse
    {
        return $this->one(fn(): array => $this->transport->post('environments/' . rawurlencode($environment) . '/domains', $request->toArray()), 'create-domain');
    }
    /** Get a domain, optionally verifying DNS and including its environment.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws TransportException For transport or hydration failures. @throws ApiException For other API errors.
     */
    public function get(string $domain, ?GetDomainRequest $request = null): DomainResponse
    {
        return $this->one(fn(): array => $this->transport->get('domains/' . rawurlencode($domain), $request?->toQuery() ?? []), 'get-domain');
    }
    /** Change the verification method for a domain.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws ValidationException For HTTP 422. @throws TransportException For transport or hydration failures.
     * @throws ApiException For other API errors.
     */
    public function update(string $domain, UpdateDomainRequest $request): DomainResponse
    {
        return $this->one(fn(): array => $this->transport->patch('domains/' . rawurlencode($domain), $request->toArray()), 'update-domain');
    }
    /** Check whether the required DNS records are configured.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws TransportException For transport or hydration failures. @throws ApiException For other API errors.
     */
    public function verify(string $domain): DomainResponse
    {
        return $this->one(fn(): array => $this->transport->post('domains/' . rawurlencode($domain) . '/verify'), 'verify-domain');
    }
    /** Delete a domain; successful HTTP 204 responses have no value.
     * @throws AuthorizationException For HTTP 403. @throws NotFoundException For HTTP 404.
     * @throws TransportException When the request fails. @throws ApiException For other API errors.
     */
    public function delete(string $domain): void
    {
        $this->transport->delete('domains/' . rawurlencode($domain));
    }
    /** @param callable(): array<string, mixed> $request */
    private function one(callable $request, string $operation): DomainResponse
    {
        $payload = $request();
        try {
            return $this->hydrator->hydrateOne($payload);
        } catch (Throwable $e) {
            throw new TransportException(sprintf('Laravel Cloud returned an invalid %s response.', $operation), 0, $e);
        }
    }
}
