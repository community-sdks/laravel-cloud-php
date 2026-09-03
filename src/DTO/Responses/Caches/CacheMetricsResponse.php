<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Caches;

use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\ScalarMetricSeries;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricSeries;

/** Cache hit, throughput, size, and bandwidth metrics. */ final readonly class CacheMetricsResponse
{/** @param list<string> $availablePeriods */ public function __construct(public MetricSeries $hitsAndMisses, public MetricSeries $throughput, public ScalarMetricSeries $size, public ScalarMetricSeries $bandwidthUsage, public string $period, public array $availablePeriods) {}
}
