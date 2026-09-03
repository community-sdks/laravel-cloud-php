<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Environments;

use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricSeries;

/** Complete environment metrics and available time periods. */
final readonly class EnvironmentMetricsResponse
{ /** @param list<string> $availablePeriods */ public function __construct(public MetricSeries $cpuUsage, public MetricSeries $memoryUsage, public MetricSeries $httpResponseCount, public MetricSeries $replicaCount, public MetricSeries $webWorkersCount, public string $period, public array $availablePeriods) {}
}
