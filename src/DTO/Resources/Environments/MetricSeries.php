<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Environments;

/** Labels, averages, and timestamped values for one metric. */
final readonly class MetricSeries
{
    /**
     * @param list<string>      $labels
     * @param list<float>       $average
     * @param list<MetricPoint> $data
     */
    public function __construct(public array $labels, public array $average, public array $data) {}
}
