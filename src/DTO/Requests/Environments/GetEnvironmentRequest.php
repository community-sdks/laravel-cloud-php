<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentInclude;

/** Relationships requested with a single environment. */
final readonly class GetEnvironmentRequest
{
    /** @param list<EnvironmentInclude> $include */
    public function __construct(public array $include = []) {}
    /** @return array<string,string> */
    public function toQuery(): array
    {
        return [] === $this->include ? [] : ['include' => implode(',', array_map(static fn(EnvironmentInclude $v): string => $v->value, $this->include))];
    }
}
