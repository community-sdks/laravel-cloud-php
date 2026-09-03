<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\EdgeNetworks;

/** Edge-network filters. */ final readonly class ListEdgeNetworksRequest
{
    public function __construct(public ?string $name = null, public ?string $domain = null, public ?string $status = null) {} /** @return array<string,string> */ public function toQuery(): array
    {
        $q = [];
        foreach (['filter[name]' => $this->name,'filter[domain]' => $this->domain,'filter[status]' => $this->status] as $k => $v) {
            if (null !== $v) {
                $q[$k] = $v;
            }
        }return$q;
    }
}
