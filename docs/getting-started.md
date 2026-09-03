# Getting Started

## Install

```bash
composer require community-sdks/laravel-cloud-php
```

The package requires PHP 8.3 or newer and does not depend on Laravel or
Illuminate. It can therefore be installed in Laravel 13 applications or used in
standalone PHP projects.

## Create a client

```php
use CommunitySDKs\LaravelCloud\Client;

$cloud = new Client(
    token: $_ENV['LARAVEL_CLOUD_TOKEN'],
);
```

The token is sent through the `Authorization: Bearer <token>` header. Keep it in
an environment variable or secret manager and do not commit it to source
control.

## Access a service

```php
$applications = $cloud->applications();
$domains = $cloud->domains();
```

Services are created lazily and reused by the client. Consult the
[service reference](services/README.md) for implemented endpoint methods.

## Strongly typed endpoint calls

As endpoints are added, request bodies are represented by dedicated immutable
request DTOs and responses by typed resource graphs. Raw associative arrays are
not part of the normal endpoint API.
