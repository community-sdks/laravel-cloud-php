<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

/** One timestamped scalar metric value. */ final readonly class ScalarMetricPoint
{
    public function __construct(public string $timestamp, public float $value) {}
}
