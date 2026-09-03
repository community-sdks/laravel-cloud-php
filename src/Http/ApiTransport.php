<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Http;

use CommunitySDKs\LaravelCloud\Exceptions\ApiException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthenticationException;
use CommunitySDKs\LaravelCloud\Exceptions\AuthorizationException;
use CommunitySDKs\LaravelCloud\Exceptions\NotFoundException;
use CommunitySDKs\LaravelCloud\Exceptions\RateLimitException;
use CommunitySDKs\LaravelCloud\Exceptions\ServerException;
use CommunitySDKs\LaravelCloud\Exceptions\TransportException;
use CommunitySDKs\LaravelCloud\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Executes authenticated Laravel Cloud HTTP requests through Guzzle.
 *
 * This is the sole boundary where generic JSON maps are exposed internally.
 * It applies shared headers, serializes request data, decodes responses, and
 * translates network and non-success responses into SDK exceptions.
 */
final class ApiTransport
{
    private readonly ClientInterface $client;

    /** Normalized API root used to resolve every relative endpoint path. */
    private readonly string $baseUri;

    public function __construct(
        ClientConfiguration $configuration,
        ?ClientInterface $client = null,
        private readonly ResponseDecoder $decoder = new ResponseDecoder(),
    ) {
        $this->baseUri = $configuration->normalizedBaseUri();
        $this->client = $client ?? new Client([
            'timeout' => $configuration->timeout,
            'connect_timeout' => $configuration->connectTimeout,
        ]);
        $this->headers = [
            'Authorization' => 'Bearer ' . $configuration->token,
            'Accept' => 'application/json',
            'User-Agent' => $configuration->userAgent,
        ];
    }

    /** @var array<string, string> */
    private readonly array $headers;

    /** @param array<string, scalar|list<scalar>|null> $query
     *  @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query);
    }

    /** @param array<string, mixed>|null $json
     *  @return array<string, mixed>
     */
    public function post(string $path, ?array $json = null): array
    {
        return $this->request('POST', $path, json: $json);
    }

    /**
     * Send a multipart request containing one binary file field.
     *
     * @return array<string, mixed>
     */
    public function postFile(string $path, string $field, string $contents, string $filename): array
    {
        return $this->request('POST', $path, multipart: [[
            'name' => $field,
            'contents' => $contents,
            'filename' => $filename,
            'headers' => ['Content-Type' => 'application/octet-stream'],
        ]]);
    }

    /** @param array<string, mixed> $json
     *  @return array<string, mixed>
     */
    public function patch(string $path, array $json): array
    {
        return $this->request('PATCH', $path, json: $json);
    }

    /** @param array<string, mixed> $json
     *  @return array<string, mixed>
     */
    public function put(string $path, array $json): array
    {
        return $this->request('PUT', $path, json: $json);
    }

    /** @return array<string, mixed> */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    /** @param array<string, scalar|list<scalar>|null> $query
     *  @param array<string, mixed>|null $json
     *  @param list<array{name: string, contents: string, filename: string, headers: array<string, string>}>|null $multipart
     *  @return array<string, mixed>
     */
    private function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        ?array $multipart = null,
    ): array {
        $options = ['headers' => $this->headers, 'http_errors' => false];
        if ([] !== $query) {
            $options['query'] = $query;
        }
        if (null !== $json) {
            $options['json'] = $json;
            $options['headers']['Content-Type'] = 'application/json';
        }
        if (null !== $multipart) {
            $options['multipart'] = $multipart;
        }

        try {
            $response = $this->client->request($method, $this->resolveUrl($path), $options);
        } catch (GuzzleException $exception) {
            throw new TransportException('The Laravel Cloud request could not be completed.', 0, $exception);
        }

        return $this->handleResponse($response);
    }

    /**
     * Resolve a service-owned relative path against the configured API root.
     *
     * Absolute paths are rejected so endpoint services cannot bypass the
     * configured Laravel Cloud environment, proxy, or mock server.
     */
    private function resolveUrl(string $path): string
    {
        if (str_contains($path, '://') || str_starts_with($path, '//')) {
            throw new InvalidArgumentException('Laravel Cloud endpoint paths must be relative.');
        }

        return $this->baseUri . ltrim($path, '/');
    }

    /** @return array<string, mixed> */
    private function handleResponse(ResponseInterface $response): array
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        if ($status >= 200 && $status < 300) {
            try {
                return $this->decoder->decode($body);
            } catch (Throwable $exception) {
                throw new TransportException('Laravel Cloud returned an unreadable response.', 0, $exception);
            }
        }

        $apiMessage = $this->extractApiMessage($body);
        $message = $apiMessage ?? sprintf('Laravel Cloud returned HTTP %d.', $status);
        $class = match (true) {
            401 === $status => AuthenticationException::class,
            403 === $status => AuthorizationException::class,
            404 === $status => NotFoundException::class,
            422 === $status => ValidationException::class,
            429 === $status => RateLimitException::class,
            $status >= 500 => ServerException::class,
            default => ApiException::class,
        };

        throw new $class($message, $status, $body, $apiMessage);
    }

    private function extractApiMessage(string $body): ?string
    {
        try {
            $decoded = $this->decoder->decode($body);
        } catch (Throwable) {
            return null;
        }

        if (isset($decoded['message']) && is_string($decoded['message'])) {
            return $decoded['message'];
        }

        $errors = $decoded['errors'] ?? null;
        if (is_array($errors) && isset($errors[0]) && is_array($errors[0])) {
            $detail = $errors[0]['detail'] ?? $errors[0]['title'] ?? null;
            return is_string($detail) ? $detail : null;
        }

        return null;
    }
}
