# Domains Service

Custom-domain creation, DNS status, verification, and deletion operations.

- PHP class: `CommunitySDKs\LaravelCloud\Services\DomainsService`
- Client accessor: `$cloud->domains()`

## Endpoints

| SDK method | HTTP endpoint | Request | Response |
| --- | --- | --- | --- |
| `list()` | `GET /environments/{environment}/domains` | `?ListDomainsRequest` | `ListDomainsResponse` |
| `create()` | `POST /environments/{environment}/domains` | `CreateDomainRequest` | `DomainResponse` |
| `get()` | `GET /domains/{domain}` | `?GetDomainRequest` | `DomainResponse` |
| `update()` | `PATCH /domains/{domain}` | `UpdateDomainRequest` | `DomainResponse` |
| `verify()` | `POST /domains/{domain}/verify` | Domain ID (`string`) | `DomainResponse` |
| `delete()` | `DELETE /domains/{domain}` | Domain ID (`string`) | `void` |

Domain and environment IDs are strings. Finite states, types, redirects,
Cloudflare strategies, verification methods, and required actions are enums.
DNS records and wildcard/WWW variants are typed DTOs.

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\CreateDomainRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Domains\GetDomainRequest;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainInclude;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainVerificationMethod;

$created = $cloud->domains()->create('environment-id', new CreateDomainRequest(
    name: 'example.com',
    verificationMethod: DomainVerificationMethod::PreVerification,
));
$domain = $cloud->domains()->get($created->data->id, new GetDomainRequest(
    verify: true,
    include: [DomainInclude::Environment],
));
$cloud->domains()->verify($domain->data->id);
$cloud->domains()->delete($domain->data->id);
```

List filters map to the four documented `filter[...]` parameters. Includes are
comma-separated. HTTP 403, 404, and 422 use the shared typed SDK exceptions.

The OpenAPI marks domain path parameters both required and nullable. Since null
cannot identify a URL resource, SDK methods require a non-null `string`.

[Back to the service index](README.md)
