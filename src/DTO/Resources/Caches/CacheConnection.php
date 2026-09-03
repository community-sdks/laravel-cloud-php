<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Caches;

/** Cache connection information and optional credentials. */ final readonly class CacheConnection
{
    public function __construct(public ?string $hostname, public ?int $port, public string $protocol, public ?string $username, public ?string $password) {}
}
