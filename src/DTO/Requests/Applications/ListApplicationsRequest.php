<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;

/** Optional filters and related resources for listing applications. */
final readonly class ListApplicationsRequest
{
    /**
     * @param list<ApplicationInclude> $include Related resources requested through JSON:API.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $region = null,
        public ?string $slug = null,
        public array $include = [],
    ) {}

    /** @return array<string, scalar|list<scalar>|null> */
    public function toQuery(): array
    {
        $query = [];
        if (null !== $this->name) {
            $query['filter[name]'] = $this->name;
        }
        if (null !== $this->region) {
            $query['filter[region]'] = $this->region;
        }
        if (null !== $this->slug) {
            $query['filter[slug]'] = $this->slug;
        }
        if ([] !== $this->include) {
            $query['include'] = implode(',', array_map(
                static fn(ApplicationInclude $include): string => $include->value,
                $this->include,
            ));
        }

        return $query;
    }
}
