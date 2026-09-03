<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Environments;

/** Values for all metric labels at one timestamp. */
final readonly class MetricPoint
{ /** @param list<float> $values */ public function __construct(public string $timestamp, public array $values) {}
}
