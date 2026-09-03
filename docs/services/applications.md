# Applications Service

Application lifecycle operations.

- PHP class: `CommunitySDKs\LaravelCloud\Services\ApplicationsService`
- Client accessor: `$cloud->applications()`

## Endpoints

| SDK method | HTTP endpoint | Request | Response |
| --- | --- | --- | --- |
| `list()` | `GET /applications` | `?ListApplicationsRequest` | `ListApplicationsResponse` |
| `get()` | `GET /applications/{application}` | `?GetApplicationRequest` | `GetApplicationResponse` |
| `create()` | `POST /applications` | `CreateApplicationRequest` | `CreateApplicationResponse` |
| `delete()` | `DELETE /applications/{application}` | Application ID (`string`) | `void` |
| `update()` | `PATCH /applications/{application}` | `UpdateApplicationRequest` | `UpdateApplicationResponse` |
| `uploadAvatar()` | `POST /applications/{application}/avatar` | `UploadApplicationAvatarRequest` | `UploadApplicationAvatarResponse` |
| `deleteAvatar()` | `DELETE /applications/{application}/avatar` | Application ID (`string`) | `void` |

## List applications

`GET /applications` · `ApplicationsService::list()`

Gets a paginated list of all applications for the authenticated organization.

### Query parameters

| API parameter | Request property | PHP type | Required | Description |
| --- | --- | --- | --- | --- |
| `filter[name]` | `name` | `?string` | No | Application name filter. |
| `filter[region]` | `region` | `?string` | No | Application region filter. |
| `filter[slug]` | `slug` | `?string` | No | Application slug filter. |
| `include` | `include` | `list<ApplicationInclude>` | No | Organization, environments, or default environment. |

The include list is serialized as a comma-separated query value as required by
the endpoint's `explode: false` OpenAPI definition.

### Response

`ListApplicationsResponse` contains:

- `data`: a typed list of `ApplicationResource` objects.
- `links`: top-level `PaginationLinks`.
- `meta`: typed `PaginationMeta` and generated `PaginatorLink` values.
- `included`: a typed union of repository, organization, environment, and
  deployment resources when supplied by Laravel Cloud.

Dates are exposed as `DateTimeImmutable`, finite values use native enums, and
resource and relationship identifiers use their native API `string` type.
To-many relationships are exposed directly as documented `list<string>` ID
properties such as `environmentIds`; no generic relationship wrapper is needed.

### Errors

- `AuthenticationException` for HTTP 401.
- `AuthorizationException` for HTTP 403.
- `ApiException` for another non-success API response.
- `TransportException` for a network failure or an invalid response payload.

### Example

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\ListApplicationsRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;

$response = $cloud->applications()->list(new ListApplicationsRequest(
    region: 'eu-central-1',
    include: [
        ApplicationInclude::Organization,
        ApplicationInclude::Environments,
    ],
));

foreach ($response->data as $application) {
    echo $application->attributes?->name ?? $application->id;
}
```

### OpenAPI inconsistency

The supplied `EnvironmentResource` schema contains an empty-string attribute
whose value is either an environment-variable object or an empty list, while
its `required` array contains `null` instead of that property name. The SDK maps
an actually returned empty-string field to `EnvironmentVariables`; the empty
list alternative becomes an empty typed collection. The malformed requirement
is treated as non-enforceable, and no alternate field name is invented.

## Get application

`GET /applications/{application}` · `ApplicationsService::get()`

Gets one application by its native string identifier. The optional
`GetApplicationRequest` accepts a `list<ApplicationInclude>` and serializes it
as the endpoint's comma-separated `include` query parameter.

The response is a `GetApplicationResponse` containing one typed
`ApplicationResource` in `data` and a typed `included` collection when Laravel
Cloud supplies related resources.

- `AuthorizationException` for HTTP 403.
- `NotFoundException` for HTTP 404.
- `ApiException` for another non-success response.
- `TransportException` for transport or response-hydration failures.

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\GetApplicationRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;

$response = $cloud->applications()->get(
    'application-id',
    new GetApplicationRequest([
        ApplicationInclude::Organization,
        ApplicationInclude::DefaultEnvironment,
    ]),
);

echo $response->data->attributes?->name ?? $response->data->id;
```

## Delete application

`DELETE /applications/{application}` · `ApplicationsService::delete()`

Deletes an application and all of its environments. The application identifier
is passed as a native `string`, and the method returns `void` because a
successful request has an HTTP 204 response with no content.

- `AuthorizationException` for HTTP 403.
- `NotFoundException` for HTTP 404.
- `ApiException` for another non-success response.
- `TransportException` when the request cannot be completed.

```php
$cloud->applications()->delete('application-id');
```

## Update application

`PATCH /applications/{application}` · `ApplicationsService::update()`

`UpdateApplicationRequest` exposes the documented optional fields: source
control provider, name, slug, default environment ID, repository, and Slack
channel. Unspecified fields are omitted. Set `clearSlackChannel: true` to send
an explicit JSON `null` without introducing a generic optional-value wrapper.

The response contains the updated typed application and optional included
resources. HTTP 401, 403, 404, and 422 responses map to their shared SDK
exceptions.

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UpdateApplicationRequest;

$response = $cloud->applications()->update(
    'application-id',
    new UpdateApplicationRequest(name: 'New Name', clearSlackChannel: true),
);
```

## Upload application avatar

`POST /applications/{application}/avatar` · `ApplicationsService::uploadAvatar()`

The avatar is supplied as binary string content and sent in the required
`multipart/form-data` `avatar` field. `UploadApplicationAvatarRequest` enforces
the documented maximum of 3072 KB and accepts an optional filename.

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UploadApplicationAvatarRequest;

$avatar = file_get_contents('/path/to/avatar.png');
if ($avatar === false) {
    throw new RuntimeException('Unable to read avatar.');
}

$response = $cloud->applications()->uploadAvatar(
    'application-id',
    new UploadApplicationAvatarRequest($avatar, 'avatar.png'),
);
```

The endpoint maps HTTP 403, 404, and 422 to `AuthorizationException`,
`NotFoundException`, and `ValidationException` respectively.

## Delete application avatar

`DELETE /applications/{application}/avatar` · `ApplicationsService::deleteAvatar()`

Removes the application's avatar and returns `void` for the successful HTTP 204
response. HTTP 403 and 404 map to `AuthorizationException` and
`NotFoundException`.

```php
$cloud->applications()->deleteAvatar('application-id');
```

## Create application

`POST /applications` · `ApplicationsService::create()`

Creates a new application from a source-control repository.

### Request fields

| API field | DTO property | PHP type | Required | Description |
| --- | --- | --- | --- | --- |
| `source_control_provider_type` | `sourceControlProviderType` | `SourceControlProviderType` | Yes | GitHub, GitLab, or Bitbucket. |
| `repository` | `repository` | `string` | Yes | Source-control repository. |
| `name` | `name` | `string` | Yes | 3–40 characters matching the documented pattern. |
| `region` | `region` | `CloudRegion` | Yes | Supported Laravel Cloud region. |
| `root_directory` | `rootDirectory` | `?string` | No | Optional repository subdirectory. |
| `cluster_id` | `clusterId` | `?string` | No | Optional dedicated cluster identifier. |

The OpenAPI `required` array omits `source_control_provider_type`, but the field
description states that it became required on March 9, 2026. The SDK therefore
requires it to produce requests valid under the current contract.

### Response and errors

The 201 response is `CreateApplicationResponse`, containing the created
`ApplicationResource` and an optional typed included-resource collection.

- `AuthenticationException` for HTTP 401.
- `AuthorizationException` for HTTP 403.
- `ValidationException` for HTTP 422.
- `ApiException` for another non-success response.
- `TransportException` for transport or response-hydration failures.

### Example

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\CreateApplicationRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Applications\SourceControlProviderType;

$response = $cloud->applications()->create(new CreateApplicationRequest(
    sourceControlProviderType: SourceControlProviderType::GitHub,
    repository: 'acme/customer-portal',
    name: 'Customer Portal',
    region: CloudRegion::EuropeWest1,
    rootDirectory: 'apps/portal',
));

echo $response->data->id;
```

[Back to the service index](README.md)
