<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** One environment-variable key and value supplied to Laravel Cloud. */
final readonly class EnvironmentVariableInput
{
    public function __construct(public string $key, public string $value)
    {
        if (1 !== preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            throw new InvalidArgumentException('Invalid environment variable key.');
        }
    } /** @return array{key:string,value:string} */ public function toArray(): array
    {
        return ['key' => $this->key,'value' => $this->value];
    }
}
