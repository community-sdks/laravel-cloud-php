<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\MetricPeriod;

/** Optional time period for environment metrics. */
final readonly class GetEnvironmentMetricsRequest
{
    public function __construct(public ?MetricPeriod $period = null) {} /** @return array<string,string> */ public function toQuery(): array
    {
        return null === $this->period ? [] : ['period' => $this->period->value];
    }
}
