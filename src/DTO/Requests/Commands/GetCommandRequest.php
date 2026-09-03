<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Commands;

use CommunitySDKs\LaravelCloud\Enums\Commands\CommandInclude;

/** Command relationship includes. */ final readonly class GetCommandRequest
{/** @param list<CommandInclude> $include */ public function __construct(public array $include = []) {} /** @return array<string,string> */ public function toQuery(): array
{
    return [] === $this->include ? [] : ['include' => implode(',', array_map(static fn(CommandInclude $v): string => $v->value, $this->include))];
}
}
