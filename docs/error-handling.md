# Error Handling

All SDK-defined failures inherit from `LaravelCloudException`, so callers may
catch broadly or handle a specific API category.

| Exception | Meaning |
| --- | --- |
| `AuthenticationException` | Laravel Cloud returned HTTP 401. |
| `AuthorizationException` | Laravel Cloud returned HTTP 403. |
| `NotFoundException` | Laravel Cloud returned HTTP 404. |
| `ValidationException` | Laravel Cloud returned HTTP 422. |
| `RateLimitException` | Laravel Cloud returned HTTP 429. |
| `ServerException` | Laravel Cloud returned an HTTP 5xx response. |
| `ApiException` | Another non-success HTTP response was returned. |
| `TransportException` | The network failed or the response could not be decoded. |

API exceptions retain the HTTP status, raw body, and parsed API message:

```php
use CommunitySDKs\LaravelCloud\Exceptions\ApiException;

try {
    // Call an implemented endpoint method.
} catch (ApiException $exception) {
    echo $exception->getStatusCode();
    echo $exception->getApiMessage() ?? 'Laravel Cloud request failed';
}
```

Each endpoint page lists the documented exceptions for that operation.
