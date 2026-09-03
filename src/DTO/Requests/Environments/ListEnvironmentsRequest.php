<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentInclude;

/** Filters and includes for an application's environments. */
final readonly class ListEnvironmentsRequest
{
    /** @param list<EnvironmentInclude> $include */
    public function __construct(public ?string $name = null, public ?string $status = null, public ?string $slug = null, public array $include = []) {}
    /** @return array<string,string> */
    public function toQuery(): array
    {
        $q = [];
        foreach (['filter[name]' => $this->name,'filter[status]' => $this->status,'filter[slug]' => $this->slug] as $k => $v) {
            if (null !== $v) {
                $q[$k] = $v;
            }
        } if ([] !== $this->include) {
            $q['include'] = implode(',', array_map(static fn(EnvironmentInclude $v): string => $v->value, $this->include));
        } return $q;
    }
}
