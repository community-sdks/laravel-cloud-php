<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration\Caches;

use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\AvailableCacheType;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheConnection;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\CacheTypeSize;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\ScalarMetricPoint;
use CommunitySDKs\LaravelCloud\DTO\Resources\Caches\ScalarMetricSeries;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricPoint;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricSeries;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\CacheMetricsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\CacheResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\ListCachesResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Caches\ListCacheTypesResponse;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheStatus;
use CommunitySDKs\LaravelCloud\Enums\Caches\CacheType;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Applications\ApplicationsHydrator;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use CommunitySDKs\LaravelCloud\Internal\Hydration\PaginationHydrator;
use UnexpectedValueException;

/** Hydrates cache resources and configuration catalogues. */ final class CachesHydrator
{
    public function __construct(private readonly ApplicationsHydrator $apps = new ApplicationsHydrator(), private readonly PaginationHydrator $pagination = new PaginationHydrator()) {} public function resource(mixed $v): CacheResource
    {
        $r = DataReader::object($v, 'cache');
        if ('caches' !== DataReader::string($r['type'] ?? null, 'type')) {
            throw new UnexpectedValueException('Expected cache.');
        }$a = DataReader::optionalObject($r, 'attributes', 'attributes');
        $ids = null;
        $rels = DataReader::optionalObject($r, 'relationships', 'relationships');
        if (null !== $rels && isset($rels['environments'])) {
            $x = DataReader::object($rels['environments'], 'environments');
            $ids = array_map(static fn(mixed $i): string => DataReader::string(DataReader::object($i, 'id')['id'] ?? null, 'id'), DataReader::list($x['data'] ?? null, 'data'));
        }return new CacheResource(DataReader::string($r['id'] ?? null, 'id'), null === $a ? null : $this->attributes($a), $ids);
    } /** @param array<string,mixed> $a */ private function attributes(array $a): CacheAttributes
    {
        $c = DataReader::object($a['connection'] ?? null, 'connection');
        $cred = $c[''] ?? null;
        if (is_array($cred) && array_is_list($cred)) {
            $cred = null;
        }$cred = null === $cred ? null : DataReader::object($cred, 'credentials');
        return new CacheAttributes(DataReader::string($a['name'] ?? null, 'name'), CacheType::from(DataReader::string($a['type'] ?? null, 'type')), CacheStatus::from(DataReader::string($a['status'] ?? null, 'status')), CloudRegion::from(DataReader::string($a['region'] ?? null, 'region')), DataReader::string($a['size'] ?? null, 'size'), DataReader::bool($a['auto_upgrade_enabled'] ?? null, 'auto'), DataReader::bool($a['is_public'] ?? null, 'public'), DataReader::bool($a['uses_hibernation'] ?? null, 'hibernation'), DataReader::nullableInt($a['hibernation_timeout'] ?? null, 'timeout'), DataReader::nullableDate($a['created_at'] ?? null, 'created'), new CacheConnection(DataReader::nullableString($c['hostname'] ?? null, 'hostname'), DataReader::nullableInt($c['port'] ?? null, 'port'), DataReader::string($c['protocol'] ?? null, 'protocol'), null === $cred ? null : DataReader::nullableString($cred['username'] ?? null, 'username'), null === $cred ? null : DataReader::nullableString($cred['password'] ?? null, 'password')));
    } /** @param array<string,mixed> $p */ public function one(array $p): CacheResponse
    {
        return new CacheResponse($this->resource($p['data'] ?? null), $this->included($p['included'] ?? null));
    } /** @param array<string,mixed> $p */ public function list(array $p): ListCachesResponse
    {
        [$l,$m] = $this->pagination->hydrate($p);
        return new ListCachesResponse(array_map($this->resource(...), DataReader::list($p['data'] ?? null, 'data')), $l, $m, $this->included($p['included'] ?? null));
    } /** @param array<string,mixed> $p */ public function types(array $p): ListCacheTypesResponse
    {
        return new ListCacheTypesResponse(array_map(static function (mixed $v): AvailableCacheType {
            $x = DataReader::object($v, 'type');
            return new AvailableCacheType(DataReader::string($x['type'] ?? null, 'type'), DataReader::string($x['label'] ?? null, 'label'), array_map(static fn(mixed $r): CloudRegion => CloudRegion::from(DataReader::string($r, 'region')), DataReader::list($x['regions'] ?? null, 'regions')), array_map(static function (mixed $s): CacheTypeSize {
                $z = DataReader::object($s, 'size');
                return new CacheTypeSize(DataReader::string($z['value'] ?? null, 'value'), DataReader::string($z['label'] ?? null, 'label'));
            }, DataReader::list($x['sizes'] ?? null, 'sizes')), DataReader::bool($x['supports_auto_upgrade'] ?? null, 'supports'));
        }, DataReader::list($p['data'] ?? null, 'data')));
    }

    /** @param array<string, mixed> $payload */
    public function metrics(array $payload): CacheMetricsResponse
    {
        $data = DataReader::object($payload['data'] ?? null, 'data');
        $meta = DataReader::object($payload['meta'] ?? null, 'meta');

        return new CacheMetricsResponse(
            $this->metricSeries($data, 'hits_and_misses'),
            $this->metricSeries($data, 'throughput'),
            $this->scalarSeries($data, 'size'),
            $this->scalarSeries($data, 'bandwidth_usage'),
            DataReader::string($meta['period'] ?? null, 'meta.period'),
            array_map(
                static fn(mixed $value): string => DataReader::string($value, 'meta.available_periods[]'),
                DataReader::list($meta['available_periods'] ?? null, 'meta.available_periods'),
            ),
        );
    }

    /** @return list<EnvironmentResource> */
    private function included(mixed $value): array
    {
        return array_map(static function (object $resource): EnvironmentResource {
            if (!$resource instanceof EnvironmentResource) {
                throw new UnexpectedValueException('Cache responses may only include environments.');
            }

            return $resource;
        }, $this->apps->hydrateIncluded($value));
    }

    /** @param array<string, mixed> $data */
    private function metricSeries(array $data, string $key): MetricSeries
    {
        $series = DataReader::object($data[$key] ?? null, 'data.' . $key);
        $number = static fn(mixed $value): float => (float) DataReader::number($value, 'metric value');

        return new MetricSeries(
            array_map(static fn(mixed $value): string => DataReader::string($value, 'metric.labels[]'), DataReader::list($series['labels'] ?? null, 'metric.labels')),
            array_map($number, DataReader::list($series['average'] ?? null, 'metric.average')),
            array_map(static function (mixed $value) use ($number): MetricPoint {
                $point = DataReader::object($value, 'metric.data[]');

                return new MetricPoint(
                    DataReader::string($point['x'] ?? null, 'metric.data[].x'),
                    array_map($number, DataReader::list($point['y'] ?? null, 'metric.data[].y')),
                );
            }, DataReader::list($series['data'] ?? null, 'metric.data')),
        );
    }

    /** @param array<string, mixed> $data */
    private function scalarSeries(array $data, string $key): ScalarMetricSeries
    {
        $series = DataReader::object($data[$key] ?? null, 'data.' . $key);

        return new ScalarMetricSeries(
            array_map(static function (mixed $value): ScalarMetricPoint {
                $point = DataReader::object($value, 'metric.data[]');

                return new ScalarMetricPoint(
                    DataReader::string($point['x'] ?? null, 'metric.data[].x'),
                    (float) DataReader::number($point['y'] ?? null, 'metric.data[].y'),
                );
            }, DataReader::list($series['data'] ?? null, 'metric.data')),
            (float) DataReader::number($series['total'] ?? null, 'metric.total'),
        );
    }
}
