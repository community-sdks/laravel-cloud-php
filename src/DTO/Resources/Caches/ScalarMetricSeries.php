<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

/** Scalar metric points and their total. */ final readonly class ScalarMetricSeries
{/** @param list<ScalarMetricPoint> $data */ public function __construct(public array $data, public float $total) {}
}
