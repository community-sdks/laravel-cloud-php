<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud;

use CommunitySDKs\LaravelCloud\Http\ApiTransport;
use CommunitySDKs\LaravelCloud\Http\ClientConfiguration;
use CommunitySDKs\LaravelCloud\Services\ApplicationsService;
use CommunitySDKs\LaravelCloud\Services\BackgroundProcessesService;
use CommunitySDKs\LaravelCloud\Services\BucketKeysService;
use CommunitySDKs\LaravelCloud\Services\CachesService;
use CommunitySDKs\LaravelCloud\Services\CommandsService;
use CommunitySDKs\LaravelCloud\Services\DatabaseClustersService;
use CommunitySDKs\LaravelCloud\Services\DatabaseRestoresService;
use CommunitySDKs\LaravelCloud\Services\DatabaseSnapshotsService;
use CommunitySDKs\LaravelCloud\Services\DatabasesService;
use CommunitySDKs\LaravelCloud\Services\DedicatedClustersService;
use CommunitySDKs\LaravelCloud\Services\DeploymentsService;
use CommunitySDKs\LaravelCloud\Services\DomainsService;
use CommunitySDKs\LaravelCloud\Services\EdgeNetworksService;
use CommunitySDKs\LaravelCloud\Services\EnvironmentsService;
use CommunitySDKs\LaravelCloud\Services\InstancesService;
use CommunitySDKs\LaravelCloud\Services\LegacyDatabasesService;
use CommunitySDKs\LaravelCloud\Services\MetaService;
use CommunitySDKs\LaravelCloud\Services\ObjectStorageBucketsService;
use CommunitySDKs\LaravelCloud\Services\SecretsService;
use CommunitySDKs\LaravelCloud\Services\UsageService;
use CommunitySDKs\LaravelCloud\Services\WebSocketApplicationsService;
use CommunitySDKs\LaravelCloud\Services\WebSocketClustersService;
use GuzzleHttp\ClientInterface;

/**
 * Main entry point for authenticated access to the Laravel Cloud API.
 *
 * The client owns one transport and lazily creates one reusable instance of
 * each typed service. It remains framework-agnostic and can be used inside or
 * outside a Laravel application.
 */
final class Client
{
    private readonly ApiTransport $transport;

    private ?ApplicationsService $applications = null;

    private ?EnvironmentsService $environments = null;

    private ?DomainsService $domains = null;

    private ?CommandsService $commands = null;

    private ?DeploymentsService $deployments = null;

    private ?InstancesService $instances = null;

    private ?BackgroundProcessesService $backgroundProcesses = null;

    private ?DatabaseClustersService $databaseClusters = null;

    private ?DatabasesService $databases = null;

    private ?DatabaseSnapshotsService $databaseSnapshots = null;

    private ?DatabaseRestoresService $databaseRestores = null;

    private ?ObjectStorageBucketsService $objectStorageBuckets = null;

    private ?BucketKeysService $bucketKeys = null;

    private ?CachesService $caches = null;

    private ?WebSocketClustersService $webSocketClusters = null;

    private ?WebSocketApplicationsService $webSocketApplications = null;

    private ?DedicatedClustersService $dedicatedClusters = null;

    private ?EdgeNetworksService $edgeNetworks = null;

    private ?SecretsService $secrets = null;

    private ?UsageService $usage = null;

    private ?MetaService $meta = null;

    private ?LegacyDatabasesService $legacyDatabases = null;

    /**
     * Configure a Laravel Cloud client.
     *
     * A custom Guzzle client is useful for middleware, observability, and
     * deterministic tests. The configured base URI is resolved by the SDK and
     * does not need to be duplicated on a custom client.
     */
    public function __construct(
        string $token,
        string $baseUri = ClientConfiguration::DEFAULT_BASE_URI,
        float $timeout = 30.0,
        float $connectTimeout = 10.0,
        ?ClientInterface $httpClient = null,
    ) {
        $configuration = new ClientConfiguration(
            token: $token,
            baseUri: $baseUri,
            timeout: $timeout,
            connectTimeout: $connectTimeout,
        );
        $this->transport = new ApiTransport($configuration, $httpClient);
    }

    /** Return the reusable applications service. */
    public function applications(): ApplicationsService
    {
        return $this->applications ??= new ApplicationsService($this->transport);
    }

    /** Return the reusable environments service. */
    public function environments(): EnvironmentsService
    {
        return $this->environments ??= new EnvironmentsService($this->transport);
    }

    /** Return the reusable domains service. */
    public function domains(): DomainsService
    {
        return $this->domains ??= new DomainsService($this->transport);
    }

    /** Return the reusable commands service. */
    public function commands(): CommandsService
    {
        return $this->commands ??= new CommandsService($this->transport);
    }

    /** Return the reusable deployments service. */
    public function deployments(): DeploymentsService
    {
        return $this->deployments ??= new DeploymentsService($this->transport);
    }

    /** Return the reusable instances service. */
    public function instances(): InstancesService
    {
        return $this->instances ??= new InstancesService($this->transport);
    }

    /** Return the reusable background-processes service. */
    public function backgroundProcesses(): BackgroundProcessesService
    {
        return $this->backgroundProcesses ??= new BackgroundProcessesService($this->transport);
    }

    /** Return the reusable database-clusters service. */
    public function databaseClusters(): DatabaseClustersService
    {
        return $this->databaseClusters ??= new DatabaseClustersService($this->transport);
    }

    /** Return the reusable databases service. */
    public function databases(): DatabasesService
    {
        return $this->databases ??= new DatabasesService($this->transport);
    }

    /** Return the reusable database-snapshots service. */
    public function databaseSnapshots(): DatabaseSnapshotsService
    {
        return $this->databaseSnapshots ??= new DatabaseSnapshotsService($this->transport);
    }

    /** Return the reusable database-restores service. */
    public function databaseRestores(): DatabaseRestoresService
    {
        return $this->databaseRestores ??= new DatabaseRestoresService($this->transport);
    }

    /** Return the reusable object-storage-buckets service. */
    public function objectStorageBuckets(): ObjectStorageBucketsService
    {
        return $this->objectStorageBuckets ??= new ObjectStorageBucketsService($this->transport);
    }

    /** Return the reusable bucket-keys service. */
    public function bucketKeys(): BucketKeysService
    {
        return $this->bucketKeys ??= new BucketKeysService($this->transport);
    }

    /** Return the reusable caches service. */
    public function caches(): CachesService
    {
        return $this->caches ??= new CachesService($this->transport);
    }

    /** Return the reusable WebSocket-clusters service. */
    public function webSocketClusters(): WebSocketClustersService
    {
        return $this->webSocketClusters ??= new WebSocketClustersService($this->transport);
    }

    /** Return the reusable WebSocket-applications service. */
    public function webSocketApplications(): WebSocketApplicationsService
    {
        return $this->webSocketApplications ??= new WebSocketApplicationsService($this->transport);
    }

    /** Return the reusable dedicated-clusters service. */
    public function dedicatedClusters(): DedicatedClustersService
    {
        return $this->dedicatedClusters ??= new DedicatedClustersService($this->transport);
    }

    /** Return the reusable edge-networks service. */
    public function edgeNetworks(): EdgeNetworksService
    {
        return $this->edgeNetworks ??= new EdgeNetworksService($this->transport);
    }

    /** Return the reusable secrets service. */
    public function secrets(): SecretsService
    {
        return $this->secrets ??= new SecretsService($this->transport);
    }

    /** Return the reusable usage service. */
    public function usage(): UsageService
    {
        return $this->usage ??= new UsageService($this->transport);
    }

    /** Return the reusable API metadata service. */
    public function meta(): MetaService
    {
        return $this->meta ??= new MetaService($this->transport);
    }

    /** Return the reusable legacy-databases service. */
    public function legacyDatabases(): LegacyDatabasesService
    {
        return $this->legacyDatabases ??= new LegacyDatabasesService($this->transport);
    }

}
