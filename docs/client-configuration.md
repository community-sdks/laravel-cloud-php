# Client Configuration

The client provides safe defaults for ordinary Laravel Cloud API access:

```php
use CommunitySDKs\LaravelCloud\Client;

$cloud = new Client(token: $_ENV['LARAVEL_CLOUD_TOKEN']);
```

Its constructor accepts:

| Parameter | Type | Default | Purpose |
| --- | --- | --- | --- |
| `token` | `string` | Required | Laravel Cloud Bearer token. |
| `baseUri` | `string` | `https://cloud.laravel.com/api/` | API root for tests, mock servers, proxies, or future official environments. |
| `timeout` | `float` | `30.0` | Total request timeout in seconds. |
| `connectTimeout` | `float` | `10.0` | Connection timeout in seconds. |
| `httpClient` | `?GuzzleHttp\ClientInterface` | `null` | Optional preconfigured Guzzle client. |

The SDK normalizes `baseUri` and resolves every service-owned relative endpoint
path against it. A custom Guzzle client does not need its own base URI:

```php
use CommunitySDKs\LaravelCloud\Client;
use GuzzleHttp\Client as GuzzleClient;

$http = new GuzzleClient();

$cloud = new Client(
    token: $_ENV['LARAVEL_CLOUD_TOKEN'],
    baseUri: 'https://mock-cloud.example/api',
    httpClient: $http,
);
```

Endpoint services never contain a hostname and cannot override this configured
API root with an absolute URL.
