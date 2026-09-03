<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Complete HSTS response-header update. */
final readonly class HstsInput
{
    public function __construct(public int|float|null $maxAge, public bool $includeSubdomains, public bool $preload)
    {
        if (null !== $maxAge && $maxAge < 0) {
            throw new InvalidArgumentException('HSTS max age cannot be negative.');
        }
    } /** @return array{max_age:int|float|null,include_subdomains:bool,preload:bool} */ public function toArray(): array
    {
        return ['max_age' => $this->maxAge,'include_subdomains' => $this->includeSubdomains,'preload' => $this->preload];
    }
}
