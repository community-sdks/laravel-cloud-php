<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Domains;

/** DNS values required to verify and route a domain. */
final readonly class DomainDnsRecords
{
    /** @param list<DnsRecord> $ssl */
    public function __construct(public array $ssl, public string $preVerification, public string $origin, public string $originCname, public string $dcv) {}
}
