# Commands Service

Run commands in an environment and inspect their execution state. Access it with `$cloud->commands()`.

## Endpoints

| SDK method | HTTP endpoint | Typed result |
| --- | --- | --- |
| `list(string $environment, ?ListCommandsRequest $request)` | `GET /environments/{environment}/commands` | `ListCommandsResponse` |
| `run(string $environment, CreateCommandRequest $request)` | `POST /environments/{environment}/commands` | `CommandResponse` |
| `get(string $command, ?GetCommandRequest $request)` | `GET /commands/{command}` | `CommandResponse` |

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\CreateCommandRequest;

$command = $cloud->commands()->run(
    'environment-id',
    new CreateCommandRequest('php artisan about'),
);
$latest = $cloud->commands()->get($command->data->id);
```

Command statuses, relationship includes, and exit codes are exposed through typed DTOs and enums.

[Back to the service index](README.md)
