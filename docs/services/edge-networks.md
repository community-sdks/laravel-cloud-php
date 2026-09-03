# Edge Networks Service

List shared and dedicated edge networks available to the organization. Access it with `$cloud->edgeNetworks()`.

## Endpoints

| SDK method | HTTP endpoint | Typed result |
| --- | --- | --- |
| `list(?ListEdgeNetworksRequest $request)` | `GET /edge-networks` | `ListEdgeNetworksResponse` |

```php
use CommunitySDKs\LaravelCloud\DTO\Requests\EdgeNetworks\ListEdgeNetworksRequest;

$networks = $cloud->edgeNetworks()->list(
    new ListEdgeNetworksRequest(status: 'available'),
);
```

Each resource exposes a typed tenancy type and zone status.

[Back to the service index](README.md)
