<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Required branch/name and optional infrastructure IDs for environment creation. */
final readonly class CreateEnvironmentRequest
{
    public function __construct(public string $branch, public string $name, public ?string $clusterId = null, public ?string $edgeNetworkId = null)
    {
        if (1 !== preg_match('/^[A-Za-z0-9 _-]{1,40}$/', $name)) {
            throw new InvalidArgumentException('Environment name must be 1–40 supported characters.');
        }
    }
    /** @return array{branch:string,name:string,cluster_id:?string,edge_network_id:?string} */
    public function toArray(): array
    {
        return ['branch' => $this->branch,'name' => $this->name,'cluster_id' => $this->clusterId,'edge_network_id' => $this->edgeNetworkId];
    }
}
