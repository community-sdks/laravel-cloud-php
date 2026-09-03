<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainInclude;

/** Optional DNS verification and include query for retrieving a domain. */
final readonly class GetDomainRequest
{
    /** @param list<DomainInclude> $include */
    public function __construct(public ?bool $verify = null, public array $include = []) {}
    /** @return array<string, bool|string> */
    public function toQuery(): array
    {
        $query = [];
        if (null !== $this->verify) {
            $query['verify'] = $this->verify;
        }
        if ([] !== $this->include) {
            $query['include'] = implode(',', array_map(static fn(DomainInclude $include): string => $include->value, $this->include));
        }
        return $query;
    }
}
