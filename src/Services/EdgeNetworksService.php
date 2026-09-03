<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\EdgeNetworks\ListEdgeNetworksRequest;
use CommunitySDKs\LaravelCloud\DTO\Resources\EdgeNetworks\EdgeNetworkResource;
use CommunitySDKs\LaravelCloud\DTO\Responses\EdgeNetworks\ListEdgeNetworksResponse;
use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\TenancyType;
use CommunitySDKs\LaravelCloud\Enums\EdgeNetworks\ZoneStatus;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use CommunitySDKs\LaravelCloud\Internal\Hydration\PaginationHydrator;

/** Lists shared and dedicated edge networks available to the organization. */ final class EdgeNetworksService extends AbstractService
{
    public function __construct(ApiTransport $t, private readonly PaginationHydrator $p = new PaginationHydrator())
    {
        parent::__construct($t);
    } /** List edge networks with optional name, domain, and status filters. */ public function list(?ListEdgeNetworksRequest $r = null): ListEdgeNetworksResponse
    {
        $v = $this->transport->get('edge-networks', $r?->toQuery() ?? []);
        [$l,$m] = $this->p->hydrate($v);
        $data = array_map(static function (mixed $x): EdgeNetworkResource {
            $z = DataReader::object($x, 'edge');
            $a = DataReader::object($z['attributes'] ?? null, 'attributes');
            return new EdgeNetworkResource(DataReader::string($z['id'] ?? null, 'id'), DataReader::string($a['name'] ?? null, 'name'), DataReader::string($a['domain'] ?? null, 'domain'), TenancyType::from(DataReader::string($a['tenancy_type'] ?? null, 'tenancy')), ZoneStatus::from(DataReader::string($a['status'] ?? null, 'status')), DataReader::nullableDate($a['created_at'] ?? null, 'created'));
        }, DataReader::list($v['data'] ?? null, 'data'));
        return new ListEdgeNetworksResponse($data,$l,$m);
    }
}
