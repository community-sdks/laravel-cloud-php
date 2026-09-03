<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

/** One SSL DNS record required for domain setup. */
final readonly class DnsRecord
{
    public function __construct(public string $type, public ?string $name, public ?string $value) {}
}
