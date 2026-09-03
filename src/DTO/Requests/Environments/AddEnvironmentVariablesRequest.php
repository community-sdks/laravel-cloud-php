<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentVariablesInsertMethod;
use InvalidArgumentException;

/** Atomic collection of environment variables to append or set. */
final readonly class AddEnvironmentVariablesRequest
{
    /** @param list<EnvironmentVariableInput> $variables */
    public function __construct(public EnvironmentVariablesInsertMethod $method, public array $variables)
    {
        if ([] === $variables || count($variables) > 200) {
            throw new InvalidArgumentException('Provide between 1 and 200 environment variables.');
        }
    }
    /** @return array{method:string,variables:list<array{key:string,value:string}>} */
    public function toArray(): array
    {
        return ['method' => $this->method->value,'variables' => array_map(static fn(EnvironmentVariableInput $v): array => $v->toArray(), $this->variables)];
    }
}
