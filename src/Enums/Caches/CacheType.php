<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Caches;

/** Cache engines supported by Laravel Cloud. */ enum CacheType: string
{
    case UpstashRedis = 'upstash_redis';
    case LaravelValkey = 'laravel_valkey';
    case AwsElasticacheRedis = 'aws_elasticache_redis';
    case AwsElasticacheValkey = 'aws_elasticache_valkey';
}
