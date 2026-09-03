<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** New vanity-domain name for an environment. */
final readonly class UpdateVanityDomainRequest
{
    public function __construct(public string $name)
    {
        $n = strlen($name);
        if ($n < 3 || $n > 100) {
            throw new InvalidArgumentException('Vanity domain must contain 3–100 characters.');
        }
    } /** @return array{name:string} */ public function toArray(): array
    {
        return ['name' => $this->name];
    }
}
