<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Commands;

use CommunitySDKs\LaravelCloud\Enums\Commands\CommandInclude;

/** Command filters and relationship includes. */ final readonly class ListCommandsRequest
{/** @param list<CommandInclude> $include */ public function __construct(public ?string $status = null, public ?string $command = null, public array $include = []) {} /** @return array<string,string> */ public function toQuery(): array
{
    $q = [];
    if (null !== $this->status) {
        $q['filter[status]'] = $this->status;
    }if (null !== $this->command) {
        $q['filter[command]'] = $this->command;
    }if ([] !== $this->include) {
        $q['include'] = implode(',', array_map(static fn(CommandInclude $v): string => $v->value, $this->include));
    }return$q;
}
}
