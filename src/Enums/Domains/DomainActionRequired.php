<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Domains;

/** DNS actions Laravel Cloud may require from the domain owner. */
enum DomainActionRequired: string
{
    case AddTxtRecords = 'add_txt_records';
    case AddDnsRecords = 'add_dns_records';
    case Failed = 'failed';
}
