<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Secret identifiers to attach to an environment. */
final readonly class AttachEnvironmentSecretsRequest
{
    /** @param list<string> $secrets */
    public function __construct(public array $secrets)
    {
        if ([] === $secrets || count($secrets) > 30) {
            throw new InvalidArgumentException('Provide between 1 and 30 secret identifiers.');
        }
    }
    /** @return array{secrets:list<string>} */ public function toArray(): array
    {
        return ['secrets' => $this->secrets];
    }
}
