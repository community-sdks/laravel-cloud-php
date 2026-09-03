<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

/** One environment-variable key and value returned by Laravel Cloud. */
final readonly class EnvironmentVariable
{
    public function __construct(public string $key, public string $value) {}
}
