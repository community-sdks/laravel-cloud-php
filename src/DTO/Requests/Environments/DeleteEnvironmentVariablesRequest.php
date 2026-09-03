<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Environment-variable keys to remove atomically. */
final readonly class DeleteEnvironmentVariablesRequest
{
    /** @param list<string> $keys */
    public function __construct(public array $keys)
    {
        if ([] === $keys) {
            throw new InvalidArgumentException('At least one environment variable key is required.');
        }foreach ($keys as $key) {
            if (1 !== preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
                throw new InvalidArgumentException('Invalid environment variable key.');
            }
        }
    }
    /** @return array{keys:list<string>} */ public function toArray(): array
    {
        return ['keys' => $this->keys];
    }
}
