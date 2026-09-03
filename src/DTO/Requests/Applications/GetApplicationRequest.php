<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\ApplicationInclude;

/** Optional related resources to include when retrieving one application. */
final readonly class GetApplicationRequest
{
    /** @param list<ApplicationInclude> $include Related resources requested through JSON:API. */
    public function __construct(
        public array $include = [],
    ) {}

    /** @return array<string, string> */
    public function toQuery(): array
    {
        if ([] === $this->include) {
            return [];
        }

        return [
            'include' => implode(',', array_map(
                static fn(ApplicationInclude $include): string => $include->value,
                $this->include,
            )),
        ];
    }
}
