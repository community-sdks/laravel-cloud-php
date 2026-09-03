# Laravel Cloud PHP SDK

A strongly typed, framework-agnostic PHP SDK for the Laravel Cloud REST API, maintained by Community SDKs.

> This is a community-maintained project and is not an official Laravel package.

## Documentation

The [documentation hub](docs/README.md) contains getting-started guidance,
authentication, client configuration, API contract policy, error handling, and a navigable
[service-by-service endpoint reference](docs/services/README.md).

Each service has its own page. As endpoint contracts are supplied and
implemented, those pages will document the PHP method, HTTP route, typed request
and response objects, exceptions, and complete usage examples.

## Requirements

- PHP 8.3 or newer (including PHP 8.5 and the PHP versions supported by Laravel 13)
- JSON extension

The SDK deliberately has no dependency on Laravel or Illuminate, so it can be used in Laravel applications and standalone PHP projects alike.

## Installation

```bash
composer require community-sdks/laravel-cloud-php
```

## Authentication and client creation

Create a client with a Laravel Cloud API token:

```php
use CommunitySDKs\LaravelCloud\Client;

$cloud = new Client(
    token: $_ENV['LARAVEL_CLOUD_TOKEN'],
);
```

The client sends the token as a Bearer credential and uses `https://cloud.laravel.com/api/` by default. A custom base URI, request timeout, connect timeout, or Guzzle client can be supplied for advanced usage and testing.

## Services

Services are typed, created lazily, and reused. The following services currently
have documented endpoint implementations:

| Service | Client accessor | Implemented operations |
| --- | --- | --- |
| [Applications](docs/services/applications.md) | `applications()` | List, get, create, update, delete, upload avatar, and delete avatar. |
| [Environments](docs/services/environments.md) | `environments()` | Lifecycle, configuration, variables, secrets, logs, metrics, edge cache, and vanity domains. |
| [Domains](docs/services/domains.md) | `domains()` | List, create, get, update, verify, and delete. |
| [Caches](docs/services/caches.md) | `caches()` | List, create, get, update, metrics, available types, and delete. |
| [Commands](docs/services/commands.md) | `commands()` | List, run, and get. |
| [Edge Networks](docs/services/edge-networks.md) | `edgeNetworks()` | List available edge networks. |

For example:

```php
$applications = $cloud->applications();
$domains = $cloud->domains();
$caches = $cloud->caches();
$commands = $cloud->commands();
```

Accessors also exist for the other known Laravel Cloud API groups so future
endpoints can be added without changing how the client is organized. An accessor
does not imply that its endpoint contract has already been implemented.

See the [complete service index](docs/services/README.md) for individual service
pages and endpoint implementation status.

## Strong typing

Endpoint request bodies use immutable request DTOs. Responses are hydrated into typed JSON:API resource graphs with scalar string identifiers, native enums, exact nullability, typed relationships, and `DateTimeImmutable` values. Generic decoded arrays remain confined to the internal HTTP boundary.

## List applications

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\ListApplicationsRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;

$applications = $cloud->applications()->list(new ListApplicationsRequest(
    region: 'eu-central-1',
    include: [ApplicationInclude::Environments],
));

foreach ($applications->data as $application) {
    echo $application->attributes?->name ?? $application->id;
}
```

See the [Applications endpoint reference](docs/services/applications.md) for
filters, included resource types, pagination, and error behavior.

## Error handling

HTTP and transport failures are exposed through SDK exceptions rooted at `LaravelCloudException`. Specific exceptions cover authentication, authorization, missing resources, validation, rate limits, server responses, and network failures. API exceptions preserve the status code, raw response body, and a parsed API message when available.

## Development

```bash
composer install
composer check
```

`composer check` verifies formatting, runs PHPStan at maximum level, and executes PHPUnit without making network requests.

## Current status

The transport, configuration, exception hierarchy, service registry, test tooling,
and static-analysis foundation are implemented. Typed endpoints are currently
available for applications, environments, domains, caches, commands, and edge
networks. Additional services will be implemented endpoint-by-endpoint from the
official Laravel Cloud OpenAPI contracts; undocumented schemas are intentionally
not guessed.
