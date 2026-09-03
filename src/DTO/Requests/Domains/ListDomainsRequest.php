<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainInclude;

/** Optional filters and includes for an environment's domain listing. */
final readonly class ListDomainsRequest
{
    /** @param list<DomainInclude> $include */
    public function __construct(public ?string $name = null, public ?string $hostnameStatus = null, public ?string $sslStatus = null, public ?string $originStatus = null, public array $include = []) {}
    /** @return array<string, string> */
    public function toQuery(): array
    {
        $query = [];
        foreach (['filter[name]' => $this->name, 'filter[hostname_status]' => $this->hostnameStatus, 'filter[ssl_status]' => $this->sslStatus, 'filter[origin_status]' => $this->originStatus] as $key => $value) {
            if (null !== $value) {
                $query[$key] = $value;
            }
        }
        if ([] !== $this->include) {
            $query['include'] = implode(',', array_map(static fn(DomainInclude $include): string => $include->value, $this->include));
        }
        return $query;
    }
}
