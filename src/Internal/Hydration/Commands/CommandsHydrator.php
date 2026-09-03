<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration\Commands;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;
use CommunitySDKs\LaravelCloud\DTO\Resources\Commands\CommandAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Commands\CommandResource;
use CommunitySDKs\LaravelCloud\Enums\Commands\CommandStatus;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use UnexpectedValueException;

/** Hydrates command resources. */ final class CommandsHydrator
{
    public function resource(mixed $v): CommandResource
    {
        $r = DataReader::object($v, 'command');
        if ('commands' !== DataReader::string($r['type'] ?? null, 'command.type')) {
            throw new UnexpectedValueException('Expected command.');
        }$a = DataReader::optionalObject($r, 'attributes', 'attributes');
        $rel = DataReader::optionalObject($r, 'relationships', 'relationships');
        $links = DataReader::object($r['links'] ?? null, 'links');
        $self = DataReader::object($links['self'] ?? null, 'links.self');
        return new CommandResource(DataReader::string($r['id'] ?? null, 'id'), null === $a ? null : new CommandAttributes(DataReader::string($a['command'] ?? null, 'command'), DataReader::nullableString($a['output'] ?? null, 'output'), CommandStatus::from(DataReader::string($a['status'] ?? null, 'status')), null === ($a['exit_code'] ?? null) ? null : DataReader::int($a['exit_code'], 'exit_code'), DataReader::nullableString($a['failure_reason'] ?? null, 'failure_reason'), DataReader::nullableDate($a['started_at'] ?? null, 'started_at'), DataReader::nullableDate($a['finished_at'] ?? null, 'finished_at'), DataReader::nullableDate($a['created_at'] ?? null, 'created_at')), $this->id($rel, 'environment', 'environments'), $this->id($rel, 'deployment', 'deployments'), $this->id($rel, 'initiator', 'users'), new Link(DataReader::string($self['href'] ?? null, 'href'), null, null, null, null, null, null));
    }/** @param array<string,mixed>|null $r */ private function id(?array $r, string $n, string $t): ?string
    {
        if (null === $r || !isset($r[$n])) {
            return null;
        }$x = DataReader::object($r[$n], 'relationship');
        if (null === ($x['data'] ?? null)) {
            return null;
        }$d = DataReader::object($x['data'], 'data');
        if ($t !== DataReader::string($d['type'] ?? null, 'type')) {
            throw new UnexpectedValueException('Invalid relationship.');
        }return DataReader::string($d['id'] ?? null, 'id');
    }
}
