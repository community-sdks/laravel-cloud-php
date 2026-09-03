# Environments Service

Environment configuration, lifecycle, observability, variables, and secrets.

- PHP class: `CommunitySDKs\LaravelCloud\Services\EnvironmentsService`
- Client accessor: `$cloud->environments()`

## Endpoints

| SDK method | HTTP endpoint | Request | Response |
| --- | --- | --- | --- |
| `list()` | `GET /applications/{application}/environments` | `?ListEnvironmentsRequest` | `ListEnvironmentsResponse` |
| `create()` | `POST /applications/{application}/environments` | `CreateEnvironmentRequest` | `EnvironmentResponse` |
| `get()` | `GET /environments/{environment}` | `?GetEnvironmentRequest` | `EnvironmentResponse` |
| `update()` | `PATCH /environments/{environment}` | `UpdateEnvironmentRequest` | `EnvironmentResponse` |
| `start()` | `POST /environments/{environment}/start` | `?StartEnvironmentRequest` | `DeploymentResponse` |
| `stop()` | `POST /environments/{environment}/stop` | — | `EnvironmentResponse` |
| `delete()` | `DELETE /environments/{environment}` | — | `void` |
| `purgeEdgeCache()` | `POST /environments/{environment}/purge-edge-cache` | `?PurgeEdgeCacheRequest` | `EnvironmentResponse` |
| `updateVanityDomain()` | `PUT /environments/{environment}/vanity-domain` | `UpdateVanityDomainRequest` | `EnvironmentResponse` |
| `addVariables()` | `POST /environments/{environment}/variables` | `AddEnvironmentVariablesRequest` | `EnvironmentResponse` |
| `deleteVariables()` | `POST /environments/{environment}/variables/delete` | `DeleteEnvironmentVariablesRequest` | `EnvironmentResponse` |
| `logs()` | `GET /environments/{environment}/logs` | `ListEnvironmentLogsRequest` | `ListEnvironmentLogsResponse` |
| `metrics()` | `GET /environments/{environment}/metrics` | `?GetEnvironmentMetricsRequest` | `EnvironmentMetricsResponse` |
| `attachSecrets()` | `POST /environments/{environment}/secrets` | `AttachEnvironmentSecretsRequest` | `EnvironmentResponse` |

Identifiers remain native strings. Includes, statuses, log categories, metric
periods, colors, runtime options, and variable insertion methods use enums.
Collections remain arrays with precise `list<Type>` PHPDoc.

## Lifecycle example

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\CreateEnvironmentRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\StartEnvironmentRequest;

$environment = $cloud->environments()->create(
    'application-id',
    new CreateEnvironmentRequest(branch: 'main', name: 'Production'),
);

$deployment = $cloud->environments()->start(
    $environment->data->id,
    new StartEnvironmentRequest(redeploy: false),
);

$cloud->environments()->stop($environment->data->id);
```

Stopping also cancels an in-progress deployment. Deletion returns `void` for
the documented HTTP 204 response and can fail validation with HTTP 422.

## Environment variables

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\AddEnvironmentVariablesRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Environments\EnvironmentVariableInput;
use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentVariablesInsertMethod;

$cloud->environments()->addVariables(
    'environment-id',
    new AddEnvironmentVariablesRequest(
        EnvironmentVariablesInsertMethod::Set,
        [new EnvironmentVariableInput('APP_ENV', 'production')],
    ),
);
```

Variable deletion is atomic: if one requested key is absent, Laravel Cloud
deletes none of them. Add requests support 1–200 variables; secret attachment
supports 1–30 string secret identifiers.

## Logs and metrics

Log requests require `DateTimeInterface` start/end values and support search,
type, case sensitivity, whole-word matching, instance IDs, and a pagination
cursor. Results contain typed log level/type enums and the type-specific
documented data map.

Metrics return typed series for CPU, memory, HTTP responses, replicas, and web
workers. Each series contains labels, numeric averages, and timestamped points.
Supported periods are `6h`, `24h`, `3d`, `7d`, and `30d` through `MetricPeriod`.

## Edge and vanity domains

Edge cache purging accepts at most one of `path`, `prefix`, or `tag`; omitting
all three purges everything. Vanity domains can only be changed once every 30
minutes, with HTTP 429 mapped to `RateLimitException`.

All documented HTTP 403, 404, and 422 responses map to
`AuthorizationException`, `NotFoundException`, and `ValidationException`.

[Back to the service index](README.md)
