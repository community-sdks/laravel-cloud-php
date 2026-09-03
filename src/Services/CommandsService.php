<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\CreateCommandRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\GetCommandRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Commands\ListCommandsRequest;
use CommunitySDKs\LaravelCloud\DTO\Responses\Commands\CommandResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Commands\ListCommandsResponse;
use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Commands\CommandsHydrator;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use CommunitySDKs\LaravelCloud\Internal\Hydration\PaginationHydrator;

/** Runs and inspects remote environment commands. */ final class CommandsService extends AbstractService
{
    public function __construct(ApiTransport $t, private readonly CommandsHydrator $h = new CommandsHydrator(), private readonly PaginationHydrator $p = new PaginationHydrator())
    {
        parent::__construct($t);
    } /** List commands for an environment. */ public function list(string $environment, ?ListCommandsRequest $r = null): ListCommandsResponse
    {
        $v = $this->transport->get('environments/' . rawurlencode($environment) . '/commands', $r?->toQuery() ?? []);
        [$l,$m] = $this->p->hydrate($v);
        return new ListCommandsResponse(array_map($this->h->resource(...), DataReader::list($v['data'] ?? null, 'data')), $l, $m);
    } /** Run a command. */ public function run(string $environment, CreateCommandRequest $r): CommandResponse
    {
        $v = $this->transport->post('environments/' . rawurlencode($environment) . '/commands', $r->toArray());
        return new CommandResponse($this->h->resource($v['data'] ?? null));
    } /** Get command details. */ public function get(string $command, ?GetCommandRequest $r = null): CommandResponse
    {
        $v = $this->transport->get('commands/' . rawurlencode($command), $r?->toQuery() ?? []);
        return new CommandResponse($this->h->resource($v['data'] ?? null));
    }
}
