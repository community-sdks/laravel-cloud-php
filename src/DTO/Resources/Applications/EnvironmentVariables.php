<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** Environment variables disclosed through the schema's empty-name field. */
final readonly class EnvironmentVariables
{
    /** @param list<EnvironmentVariable> $environmentVariables */
    public function __construct(public array $environmentVariables) {}
}
