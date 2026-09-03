<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration\Applications;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginatorLink;
use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\ApplicationRelationships;
use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\DeploymentRelationships;
use CommunitySDKs\LaravelCloud\DTO\Relationships\Applications\EnvironmentRelationships;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationRepository;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ApplicationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\DeploymentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentVariable;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentVariables;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\FirewallSettings;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\HstsSettings;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\NetworkSettings;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\OrganizationResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RateLimitSettings;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\RepositoryResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\ResponseHeaderSettings;
use CommunitySDKs\LaravelCloud\DTO\Responses\Applications\ListApplicationsResponse;
use CommunitySDKs\LaravelCloud\Enums\Applications\BotControlCategory;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Applications\DeploymentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\EnvironmentStatus;
use CommunitySDKs\LaravelCloud\Enums\Applications\NodeVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\PhpMajorVersion;
use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitLevel;
use CommunitySDKs\LaravelCloud\Enums\Applications\RateLimitPerMinute;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use UnexpectedValueException;

/** Hydrates application resources and their supported included resource graph. */
final class ApplicationsHydrator
{
    /** @param array<string, mixed> $payload */
    public function hydrate(array $payload): ListApplicationsResponse
    {
        $data = array_map($this->application(...), DataReader::list($payload['data'] ?? null, 'data'));
        $included = array_map($this->included(...), DataReader::list($payload['included'] ?? [], 'included'));

        return new ListApplicationsResponse(
            $data,
            $this->paginationLinks(DataReader::object($payload['links'] ?? null, 'links')),
            $this->paginationMeta(DataReader::object($payload['meta'] ?? null, 'meta')),
            $included,
        );
    }

    /** Hydrate one application resource from decoded JSON. */
    public function hydrateApplication(mixed $resource): ApplicationResource
    {
        return $this->application($resource);
    }

    /**
     * Hydrate the optional JSON:API included collection.
     *
     * @return list<RepositoryResource|OrganizationResource|EnvironmentResource|DeploymentResource>
     */
    public function hydrateIncluded(mixed $included): array
    {
        return array_map($this->included(...), DataReader::list($included ?? [], 'included'));
    }

    private function application(mixed $value): ApplicationResource
    {
        $resource = $this->resource($value, 'applications');
        $attributes = DataReader::optionalObject($resource, 'attributes', 'application.attributes');
        $relationships = DataReader::optionalObject($resource, 'relationships', 'application.relationships');

        return new ApplicationResource(
            DataReader::string($resource['id'] ?? null, 'application.id'),
            null === $attributes ? null : $this->applicationAttributes($attributes),
            null === $relationships ? null : $this->applicationRelationships($relationships),
        );
    }

    /** @param array<string, mixed> $a */
    private function applicationAttributes(array $a): ApplicationAttributes
    {
        $repository = $a['repository'] ?? null;

        return new ApplicationAttributes(
            DataReader::string($a['name'] ?? null, 'application.attributes.name'),
            DataReader::string($a['slug'] ?? null, 'application.attributes.slug'),
            CloudRegion::from(DataReader::string($a['region'] ?? null, 'application.attributes.region')),
            DataReader::nullableString($a['root_directory'] ?? null, 'application.attributes.root_directory'),
            DataReader::nullableString($a['slack_channel'] ?? null, 'application.attributes.slack_channel'),
            DataReader::string($a['avatar_url'] ?? null, 'application.attributes.avatar_url'),
            DataReader::nullableDate($a['created_at'] ?? null, 'application.attributes.created_at'),
            null === $repository ? null : $this->applicationRepository(DataReader::object($repository, 'application.attributes.repository')),
        );
    }

    /** @param array<string, mixed> $value */
    private function applicationRepository(array $value): ApplicationRepository
    {
        return new ApplicationRepository(
            DataReader::string($value['full_name'] ?? null, 'application.attributes.repository.full_name'),
            DataReader::string($value['default_branch'] ?? null, 'application.attributes.repository.default_branch'),
        );
    }

    /** @param array<string, mixed> $r */
    private function applicationRelationships(array $r): ApplicationRelationships
    {
        return new ApplicationRelationships(
            $this->toOne($r, 'repository', 'repositories'),
            $this->toOne($r, 'organization', 'organizations'),
            $this->toMany($r, 'environments', 'environments'),
            $this->toMany($r, 'deployments', 'deployments'),
            $this->toOne($r, 'defaultEnvironment', 'environments'),
        );
    }

    private function included(mixed $value): RepositoryResource|OrganizationResource|EnvironmentResource|DeploymentResource
    {
        $resource = DataReader::object($value, 'included[]');

        return match (DataReader::string($resource['type'] ?? null, 'included[].type')) {
            'repositories' => $this->repository($resource),
            'organizations' => $this->organization($resource),
            'environments' => $this->environment($resource),
            'deployments' => $this->deployment($resource),
            default => throw new UnexpectedValueException('included[] has an unsupported resource type.'),
        };
    }

    /** @param array<string, mixed> $resource */
    private function repository(array $resource): RepositoryResource
    {
        $a = DataReader::optionalObject($resource, 'attributes', 'repository.attributes');
        return new RepositoryResource(
            DataReader::string($resource['id'] ?? null, 'repository.id'),
            null === $a ? null : new RepositoryAttributes(DataReader::string($a['name'] ?? null, 'repository.attributes.name')),
        );
    }

    /** @param array<string, mixed> $resource */
    private function organization(array $resource): OrganizationResource
    {
        $a = DataReader::optionalObject($resource, 'attributes', 'organization.attributes');
        return new OrganizationResource(
            DataReader::string($resource['id'] ?? null, 'organization.id'),
            null === $a ? null : new OrganizationAttributes(
                DataReader::string($a['name'] ?? null, 'organization.attributes.name'),
                DataReader::string($a['slug'] ?? null, 'organization.attributes.slug'),
            ),
        );
    }

    /** @param array<string, mixed> $resource */
    private function environment(array $resource): EnvironmentResource
    {
        $a = DataReader::optionalObject($resource, 'attributes', 'environment.attributes');
        $r = DataReader::optionalObject($resource, 'relationships', 'environment.relationships');
        return new EnvironmentResource(
            DataReader::string($resource['id'] ?? null, 'environment.id'),
            null === $a ? null : $this->environmentAttributes($a),
            null === $r ? null : $this->environmentRelationships($r),
            $this->selfLink(DataReader::object($resource['links'] ?? null, 'environment.links')),
        );
    }

    /** @param array<string, mixed> $a */
    private function environmentAttributes(array $a): EnvironmentAttributes
    {
        return new EnvironmentAttributes(
            DataReader::string($a['name'] ?? null, 'environment.attributes.name'),
            DataReader::string($a['slug'] ?? null, 'environment.attributes.slug'),
            EnvironmentStatus::from(DataReader::string($a['status'] ?? null, 'environment.attributes.status')),
            DataReader::bool($a['created_from_automation'] ?? null, 'environment.attributes.created_from_automation'),
            DataReader::string($a['vanity_domain'] ?? null, 'environment.attributes.vanity_domain'),
            PhpMajorVersion::from(DataReader::string($a['php_major_version'] ?? null, 'environment.attributes.php_major_version')),
            DataReader::nullableString($a['build_command'] ?? null, 'environment.attributes.build_command'),
            NodeVersion::from(DataReader::string($a['node_version'] ?? null, 'environment.attributes.node_version')),
            DataReader::nullableString($a['deploy_command'] ?? null, 'environment.attributes.deploy_command'),
            DataReader::bool($a['uses_octane'] ?? null, 'environment.attributes.uses_octane'),
            DataReader::bool($a['uses_hibernation'] ?? null, 'environment.attributes.uses_hibernation'),
            DataReader::nullableInt($a['hibernation_wake_up_interval'] ?? null, 'environment.attributes.hibernation_wake_up_interval'),
            DataReader::bool($a['uses_push_to_deploy'] ?? null, 'environment.attributes.uses_push_to_deploy'),
            DataReader::bool($a['uses_deploy_hook'] ?? null, 'environment.attributes.uses_deploy_hook'),
            array_key_exists('', $a) ? $this->environmentVariables($a['']) : null,
            $this->networkSettings(DataReader::object($a['network_settings'] ?? null, 'environment.attributes.network_settings')),
            DataReader::nullableDate($a['created_at'] ?? null, 'environment.attributes.created_at'),
        );
    }

    private function environmentVariables(mixed $value): EnvironmentVariables
    {
        if (is_array($value) && array_is_list($value)) {
            if ([] !== $value) {
                throw new UnexpectedValueException('environment.attributes[""] must be an empty list.');
            }
            return new EnvironmentVariables([]);
        }
        $object = DataReader::object($value, 'environment.attributes[""]');
        $variables = array_map(static function (mixed $item): EnvironmentVariable {
            $v = DataReader::object($item, 'environment_variables[]');
            return new EnvironmentVariable(
                DataReader::string($v['key'] ?? null, 'environment_variables[].key'),
                DataReader::string($v['value'] ?? null, 'environment_variables[].value'),
            );
        }, DataReader::list($object['environment_variables'] ?? null, 'environment_variables'));
        return new EnvironmentVariables($variables);
    }

    /** @param array<string, mixed> $n */
    private function networkSettings(array $n): NetworkSettings
    {
        $cache = DataReader::object($n['cache'] ?? null, 'network_settings.cache');
        $headers = DataReader::object($n['response_headers'] ?? null, 'network_settings.response_headers');
        $hsts = DataReader::object($headers['hsts'] ?? null, 'network_settings.response_headers.hsts');
        $firewall = DataReader::object($n['firewall'] ?? null, 'network_settings.firewall');
        $rate = DataReader::object($firewall['rate_limit'] ?? null, 'network_settings.firewall.rate_limit');
        $bots = array_map(
            static fn(mixed $bot): BotControlCategory => BotControlCategory::from(DataReader::string($bot, 'bot_categories[]')),
            DataReader::list($firewall['bot_categories'] ?? null, 'network_settings.firewall.bot_categories'),
        );

        return new NetworkSettings(
            DataReader::string($cache['strategy'] ?? null, 'network_settings.cache.strategy'),
            new ResponseHeaderSettings(
                DataReader::string($headers['frame'] ?? null, 'network_settings.response_headers.frame'),
                DataReader::string($headers['content_type'] ?? null, 'network_settings.response_headers.content_type'),
                new HstsSettings(
                    DataReader::int($hsts['max_age'] ?? null, 'hsts.max_age'),
                    DataReader::bool($hsts['include_subdomains'] ?? null, 'hsts.include_subdomains'),
                    DataReader::bool($hsts['preload'] ?? null, 'hsts.preload'),
                ),
            ),
            new FirewallSettings(
                $bots,
                new RateLimitSettings(
                    DataReader::bool(DataReader::member($rate, '429'), 'rate_limit.429'),
                    RateLimitLevel::from(DataReader::string($rate['level'] ?? null, 'rate_limit.level')),
                    RateLimitPerMinute::from(DataReader::int($rate['per_minute'] ?? null, 'rate_limit.per_minute')),
                    DataReader::bool($rate['4xx'] ?? null, 'rate_limit.4xx'),
                ),
                DataReader::string($firewall['under_attack_mode_started_at'] ?? null, 'firewall.under_attack_mode_started_at'),
                DataReader::bool($firewall['block_path'] ?? null, 'firewall.block_path'),
            ),
            DataReader::bool($n['content_converter'] ?? null, 'network_settings.content_converter'),
        );
    }

    /** @param array<string, mixed> $r */
    private function environmentRelationships(array $r): EnvironmentRelationships
    {
        return new EnvironmentRelationships(
            $this->toOne($r, 'application', 'applications'),
            $this->toOne($r, 'branch', 'branches'),
            $this->toMany($r, 'deployments', 'deployments'),
            $this->toOne($r, 'currentDeployment', 'deployments'),
            $this->toMany($r, 'domains', 'domains'),
            $this->toOne($r, 'primaryDomain', 'domains'),
            $this->toMany($r, 'instances', 'instances'),
            $this->toOne($r, 'database', 'databaseSchemas'),
            $this->toOne($r, 'cache', 'caches'),
            $this->toMany($r, 'buckets', 'filesystems'),
            $this->toOne($r, 'websocketApplication', 'websocketApplications'),
            $this->toMany($r, 'secrets', 'secrets'),
        );
    }

    /** @param array<string, mixed> $resource */
    private function deployment(array $resource): DeploymentResource
    {
        $a = DataReader::optionalObject($resource, 'attributes', 'deployment.attributes');
        $r = DataReader::optionalObject($resource, 'relationships', 'deployment.relationships');
        return new DeploymentResource(
            DataReader::string($resource['id'] ?? null, 'deployment.id'),
            null === $a ? null : $this->deploymentAttributes($a),
            null === $r ? null : new DeploymentRelationships(
                $this->toOne($r, 'environment', 'environments'),
                $this->toOne($r, 'initiator', 'users'),
            ),
            $this->selfLink(DataReader::object($resource['links'] ?? null, 'deployment.links')),
        );
    }

    /** @param array<string, mixed> $a */
    private function deploymentAttributes(array $a): DeploymentAttributes
    {
        return new DeploymentAttributes(
            DeploymentStatus::from(DataReader::string($a['status'] ?? null, 'deployment.attributes.status')),
            DataReader::string($a['branch_name'] ?? null, 'deployment.attributes.branch_name'),
            DataReader::string($a['commit_hash'] ?? null, 'deployment.attributes.commit_hash'),
            DataReader::string($a['commit_message'] ?? null, 'deployment.attributes.commit_message'),
            DataReader::nullableString($a['commit_author'] ?? null, 'deployment.attributes.commit_author'),
            DataReader::nullableString($a['failure_reason'] ?? null, 'deployment.attributes.failure_reason'),
            PhpMajorVersion::from(DataReader::string($a['php_major_version'] ?? null, 'deployment.attributes.php_major_version')),
            DataReader::nullableString($a['build_command'] ?? null, 'deployment.attributes.build_command'),
            NodeVersion::from(DataReader::string($a['node_version'] ?? null, 'deployment.attributes.node_version')),
            DataReader::bool($a['uses_octane'] ?? null, 'deployment.attributes.uses_octane'),
            DataReader::bool($a['uses_hibernation'] ?? null, 'deployment.attributes.uses_hibernation'),
            DataReader::nullableInt($a['hibernation_wake_up_interval'] ?? null, 'deployment.attributes.hibernation_wake_up_interval'),
            DataReader::nullableDate($a['started_at'] ?? null, 'deployment.attributes.started_at'),
            DataReader::nullableDate($a['finished_at'] ?? null, 'deployment.attributes.finished_at'),
        );
    }

    /** @param array<string, mixed> $value */
    private function selfLink(array $value): Link
    {
        return $this->link(DataReader::object($value['self'] ?? null, 'links.self'));
    }

    /** @param array<string, mixed> $value */
    private function link(array $value): Link
    {
        $hreflang = $value['hreflang'] ?? null;
        if (is_array($hreflang)) {
            $hreflang = array_map(static fn(mixed $item): string => DataReader::string($item, 'link.hreflang[]'), DataReader::list($hreflang, 'link.hreflang'));
        } elseif (null !== $hreflang) {
            $hreflang = DataReader::string($hreflang, 'link.hreflang');
        }
        $meta = array_key_exists('meta', $value) ? DataReader::object($value['meta'], 'link.meta') : null;
        return new Link(
            DataReader::string($value['href'] ?? null, 'link.href'),
            DataReader::nullableString($value['rel'] ?? null, 'link.rel'),
            DataReader::nullableString($value['describedby'] ?? null, 'link.describedby'),
            DataReader::nullableString($value['title'] ?? null, 'link.title'),
            DataReader::nullableString($value['type'] ?? null, 'link.type'),
            $hreflang,
            $meta,
        );
    }

    /** @param array<string, mixed> $value */
    private function paginationLinks(array $value): PaginationLinks
    {
        return new PaginationLinks(
            array_key_exists('first', $value) ? DataReader::string($value['first'], 'links.first') : null,
            array_key_exists('last', $value) ? DataReader::string($value['last'], 'links.last') : null,
            array_key_exists('prev', $value) ? DataReader::string($value['prev'], 'links.prev') : null,
            array_key_exists('next', $value) ? DataReader::string($value['next'], 'links.next') : null,
        );
    }

    /** @param array<string, mixed> $value */
    private function paginationMeta(array $value): PaginationMeta
    {
        $links = array_map(static function (mixed $item): PaginatorLink {
            $link = DataReader::object($item, 'meta.links[]');
            return new PaginatorLink(
                DataReader::nullableString($link['url'] ?? null, 'meta.links[].url'),
                DataReader::string($link['label'] ?? null, 'meta.links[].label'),
                DataReader::bool($link['active'] ?? null, 'meta.links[].active'),
            );
        }, DataReader::list($value['links'] ?? null, 'meta.links'));
        return new PaginationMeta(
            DataReader::int($value['current_page'] ?? null, 'meta.current_page'),
            DataReader::nullableInt($value['from'] ?? null, 'meta.from'),
            DataReader::int($value['last_page'] ?? null, 'meta.last_page'),
            $links,
            DataReader::nullableString($value['path'] ?? null, 'meta.path'),
            DataReader::int($value['per_page'] ?? null, 'meta.per_page'),
            DataReader::nullableInt($value['to'] ?? null, 'meta.to'),
            DataReader::int($value['total'] ?? null, 'meta.total'),
        );
    }

    /** @return array<string, mixed> */
    private function resource(mixed $value, string $type): array
    {
        $resource = DataReader::object($value, $type . '[]');
        if ($type !== DataReader::string($resource['type'] ?? null, $type . '[].type')) {
            throw new UnexpectedValueException(sprintf('Expected resource type %s.', $type));
        }
        return $resource;
    }

    /** @param array<string, mixed> $relationships */
    private function toOne(array $relationships, string $name, string $type): ?string
    {
        if (!array_key_exists($name, $relationships)) {
            return null;
        }
        $relationship = DataReader::object($relationships[$name], 'relationships.' . $name);
        if (!array_key_exists('data', $relationship)) {
            throw new UnexpectedValueException('relationships.' . $name . '.data is required.');
        }
        if (null === $relationship['data']) {
            return null;
        }
        $identifier = $this->resource($relationship['data'], $type);
        return DataReader::string($identifier['id'] ?? null, 'relationships.' . $name . '.data.id');
    }

    /**
     * @param array<string, mixed> $relationships
     *
     * @return list<string>|null
     */
    private function toMany(array $relationships, string $name, string $type): ?array
    {
        if (!array_key_exists($name, $relationships)) {
            return null;
        }
        $relationship = DataReader::object($relationships[$name], 'relationships.' . $name);
        $items = array_map(function (mixed $item) use ($name, $type): string {
            $identifier = $this->resource($item, $type);
            return DataReader::string($identifier['id'] ?? null, 'relationships.' . $name . '.data[].id');
        }, DataReader::list($relationship['data'] ?? null, 'relationships.' . $name . '.data'));
        return $items;
    }
}
