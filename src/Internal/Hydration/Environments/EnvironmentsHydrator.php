<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration\Environments;

use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginatorLink;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\LogResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricPoint;
use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\MetricSeries;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\DeploymentResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\EnvironmentMetricsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\EnvironmentResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\ListEnvironmentLogsResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Environments\ListEnvironmentsResponse;
use CommunitySDKs\LaravelCloud\Enums\Environments\LogLevel;
use CommunitySDKs\LaravelCloud\Enums\Environments\NormalizedLogType;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Applications\ApplicationsHydrator;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use UnexpectedValueException;

/** Hydrates environment, deployment, log, and metric endpoint documents. */
final class EnvironmentsHydrator
{
    public function __construct(private readonly ApplicationsHydrator $applications = new ApplicationsHydrator()) {}
    /** @param array<string,mixed> $payload */
    public function one(array $payload): EnvironmentResponse
    {
        return new EnvironmentResponse($this->applications->hydrateEnvironment($payload['data'] ?? null), $this->applications->hydrateIncluded($payload['included'] ?? null));
    }
    /** @param array<string,mixed> $payload */
    public function deployment(array $payload): DeploymentResponse
    {
        $included = $this->applications->hydrateIncluded($payload['included'] ?? null);
        foreach ($included as $item) {
            if (!$item instanceof EnvironmentResource) {
                throw new UnexpectedValueException('Start environment may only include environments.');
            }
        }
        /** @var list<EnvironmentResource> $included */
        return new DeploymentResponse($this->applications->hydrateDeployment($payload['data'] ?? null), $included);
    }
    /** @param array<string,mixed> $payload */
    public function list(array $payload): ListEnvironmentsResponse
    {
        $data = array_map($this->applications->hydrateEnvironment(...), DataReader::list($payload['data'] ?? null, 'data'));
        $links = DataReader::object($payload['links'] ?? null, 'links');
        $meta = DataReader::object($payload['meta'] ?? null, 'meta');
        $paginator = array_map(static function (mixed $v): PaginatorLink {
            $x = DataReader::object($v, 'meta.links[]');
            return new PaginatorLink(DataReader::nullableString($x['url'] ?? null, 'meta.links[].url'), DataReader::string($x['label'] ?? null, 'meta.links[].label'), DataReader::bool($x['active'] ?? null, 'meta.links[].active'));
        }, DataReader::list($meta['links'] ?? null, 'meta.links'));
        return new ListEnvironmentsResponse($data, new PaginationLinks(DataReader::nullableString($links['first'] ?? null, 'links.first'), DataReader::nullableString($links['last'] ?? null, 'links.last'), DataReader::nullableString($links['prev'] ?? null, 'links.prev'), DataReader::nullableString($links['next'] ?? null, 'links.next')), new PaginationMeta(DataReader::int($meta['current_page'] ?? null, 'meta.current_page'), DataReader::nullableInt($meta['from'] ?? null, 'meta.from'), DataReader::int($meta['last_page'] ?? null, 'meta.last_page'), $paginator, DataReader::nullableString($meta['path'] ?? null, 'meta.path'), DataReader::int($meta['per_page'] ?? null, 'meta.per_page'), DataReader::nullableInt($meta['to'] ?? null, 'meta.to'), DataReader::int($meta['total'] ?? null, 'meta.total')), $this->applications->hydrateIncluded($payload['included'] ?? null));
    }
    /** @param array<string,mixed> $payload */
    public function logs(array $payload): ListEnvironmentLogsResponse
    {
        $data = array_map(static function (mixed $v): LogResource {
            $x = DataReader::object($v, 'data[]');
            $raw = $x['data'] ?? null;
            if (null !== $raw) {
                $raw = DataReader::object($raw, 'data[].data');
            }return new LogResource(DataReader::string($x['message'] ?? null, 'data[].message'), LogLevel::from(DataReader::string($x['level'] ?? null, 'data[].level')), NormalizedLogType::from(DataReader::string($x['type'] ?? null, 'data[].type')), DataReader::string($x['logged_at'] ?? null, 'data[].logged_at'), $raw);
        }, DataReader::list($payload['data'] ?? null, 'data'));
        $m = DataReader::object($payload['meta'] ?? null, 'meta');
        return new ListEnvironmentLogsResponse($data, DataReader::string($m['cursor'] ?? null, 'meta.cursor'), DataReader::string($m['type'] ?? null, 'meta.type'), DataReader::string($m['from'] ?? null, 'meta.from'), DataReader::string($m['to'] ?? null, 'meta.to'));
    }
    /** @param array<string,mixed> $payload */
    public function metrics(array $payload): EnvironmentMetricsResponse
    {
        $d = DataReader::object($payload['data'] ?? null, 'data');
        $m = DataReader::object($payload['meta'] ?? null, 'meta');
        return new EnvironmentMetricsResponse($this->series($d, 'cpu_usage'), $this->series($d, 'memory_usage'), $this->series($d, 'http_response_count'), $this->series($d, 'replica_count'), $this->series($d, 'web_workers_count'), DataReader::string($m['period'] ?? null, 'meta.period'), array_map(static fn(mixed $v): string => DataReader::string($v, 'meta.available_periods[]'), DataReader::list($m['available_periods'] ?? null, 'meta.available_periods')));
    }
    /** @param array<string,mixed> $data */
    private function series(array $data, string $key): MetricSeries
    {
        $s = DataReader::object($data[$key] ?? null, 'data.' . $key);
        $numbers = static fn(mixed $v): float => (float) DataReader::number($v, 'metric value');
        $points = array_map(static function (mixed $v) use ($numbers): MetricPoint {
            $p = DataReader::object($v, 'metric.data[]');
            return new MetricPoint(DataReader::string($p['x'] ?? null, 'metric.data[].x'), array_map($numbers, DataReader::list($p['y'] ?? null, 'metric.data[].y')));
        }, DataReader::list($s['data'] ?? null,'metric.data'));
        return new MetricSeries(array_map(static fn(mixed $v): string => DataReader::string($v,'metric.labels[]'),DataReader::list($s['labels'] ?? null,'metric.labels')),array_map($numbers,DataReader::list($s['average'] ?? null,'metric.average')),$points);
    }
}
