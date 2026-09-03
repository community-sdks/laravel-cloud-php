<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** Supported Cloudflare integration strategies. */
enum DomainCloudflareStrategy: string
{
    case DnsProxy = 'dns_proxy';
    case Dns = 'dns';
    case None = 'none';
}
