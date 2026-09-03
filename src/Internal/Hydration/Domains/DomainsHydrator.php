<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Internal\Hydration\Domains;

use CommunitySDKs\LaravelCloud\DTO\Common\Link;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationLinks;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginationMeta;
use CommunitySDKs\LaravelCloud\DTO\Common\PaginatorLink;
use CommunitySDKs\LaravelCloud\DTO\Resources\Applications\EnvironmentResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DnsRecord;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DomainAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DomainDnsRecords;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DomainResource;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\DomainVariant;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\SimplifiedDomainAttributes;
use CommunitySDKs\LaravelCloud\DTO\Resources\Domains\SimplifiedDomainResource;
use CommunitySDKs\LaravelCloud\DTO\Responses\Domains\DomainResponse;
use CommunitySDKs\LaravelCloud\DTO\Responses\Domains\ListDomainsResponse;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainActionRequired;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainCloudflareStrategy;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainRedirect;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainStatus;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainType;
use CommunitySDKs\LaravelCloud\Internal\Hydration\Applications\ApplicationsHydrator;
use CommunitySDKs\LaravelCloud\Internal\Hydration\DataReader;
use UnexpectedValueException;

/** Hydrates complete and simplified domain JSON:API documents. */
final class DomainsHydrator
{
    public function __construct(private readonly ApplicationsHydrator $applicationsHydrator = new ApplicationsHydrator()) {}

    /** @param array<string, mixed> $payload */
    public function hydrateOne(array $payload): DomainResponse
    {
        return new DomainResponse($this->domain($payload['data'] ?? null), $this->included($payload['included'] ?? null));
    }

    /** @param array<string, mixed> $payload */
    public function hydrateList(array $payload): ListDomainsResponse
    {
        return new ListDomainsResponse(
            array_map($this->simplifiedDomain(...), DataReader::list($payload['data'] ?? null, 'data')),
            $this->paginationLinks(DataReader::object($payload['links'] ?? null, 'links')),
            $this->paginationMeta(DataReader::object($payload['meta'] ?? null, 'meta')),
            $this->included($payload['included'] ?? null),
        );
    }

    private function domain(mixed $value): DomainResource
    {
        $resource = $this->resource($value);
        $attributes = DataReader::optionalObject($resource, 'attributes', 'domain.attributes');
        return new DomainResource(
            DataReader::string($resource['id'] ?? null, 'domain.id'),
            null === $attributes ? null : $this->attributes($attributes),
            $this->environmentId($resource),
            $this->selfLink($resource),
        );
    }

    private function simplifiedDomain(mixed $value): SimplifiedDomainResource
    {
        $resource = $this->resource($value);
        $a = DataReader::optionalObject($resource, 'attributes', 'domain.attributes');
        $attributes = null === $a ? null : new SimplifiedDomainAttributes(
            DataReader::string($a['name'] ?? null, 'domain.attributes.name'),
            DomainType::from(DataReader::string($a['type'] ?? null, 'domain.attributes.type')),
            DomainStatus::from(DataReader::string($a['hostname_status'] ?? null, 'domain.attributes.hostname_status')),
            DomainStatus::from(DataReader::string($a['ssl_status'] ?? null, 'domain.attributes.ssl_status')),
            DomainStatus::from(DataReader::string($a['origin_status'] ?? null, 'domain.attributes.origin_status')),
            null === ($a['redirect'] ?? null) ? null : DomainRedirect::from(DataReader::string($a['redirect'], 'domain.attributes.redirect')),
            DataReader::nullableDate($a['last_verified_at'] ?? null, 'domain.attributes.last_verified_at'),
            DataReader::nullableDate($a['created_at'] ?? null, 'domain.attributes.created_at'),
        );
        return new SimplifiedDomainResource(DataReader::string($resource['id'] ?? null, 'domain.id'), $attributes, $this->environmentId($resource), $this->selfLink($resource));
    }

    /** @param array<string, mixed> $a */
    private function attributes(array $a): DomainAttributes
    {
        return new DomainAttributes(
            DataReader::string($a['name'] ?? null, 'domain.attributes.name'),
            DomainType::from(DataReader::string($a['type'] ?? null, 'domain.attributes.type')),
            DomainStatus::from(DataReader::string($a['hostname_status'] ?? null, 'domain.attributes.hostname_status')),
            DomainStatus::from(DataReader::string($a['ssl_status'] ?? null, 'domain.attributes.ssl_status')),
            DomainStatus::from(DataReader::string($a['origin_status'] ?? null, 'domain.attributes.origin_status')),
            null === ($a['redirect'] ?? null) ? null : DomainRedirect::from(DataReader::string($a['redirect'], 'domain.attributes.redirect')),
            null === ($a['cloudflare_strategy'] ?? null) ? null : DomainCloudflareStrategy::from(DataReader::string($a['cloudflare_strategy'], 'domain.attributes.cloudflare_strategy')),
            array_key_exists('downtime', $a) ? $this->nullableBool($a['downtime'], 'domain.attributes.downtime') : null,
            DataReader::bool($a['wildcard_enabled'] ?? null, 'domain.attributes.wildcard_enabled'),
            $this->dnsRecords(DataReader::object($a['dns_records'] ?? null, 'domain.attributes.dns_records')),
            null === ($a['wildcard'] ?? null) ? null : $this->variant(DataReader::object($a['wildcard'], 'domain.attributes.wildcard')),
            null === ($a['www'] ?? null) ? null : $this->variant(DataReader::object($a['www'], 'domain.attributes.www')),
            null === ($a['action_required'] ?? null) ? null : DomainActionRequired::from(DataReader::string($a['action_required'], 'domain.attributes.action_required')),
            DataReader::nullableDate($a['last_verified_at'] ?? null, 'domain.attributes.last_verified_at'),
            DataReader::nullableDate($a['created_at'] ?? null, 'domain.attributes.created_at'),
        );
    }

    /** @param array<string, mixed> $value */
    private function dnsRecords(array $value): DomainDnsRecords
    {
        $ssl = array_map(static function (mixed $item): DnsRecord {
            $record = DataReader::object($item, 'dns_records.ssl[]');
            $type = DataReader::string($record['type'] ?? null, 'dns_records.ssl[].type');
            if (!in_array($type, ['CNAME', 'TXT'], true)) {
                throw new UnexpectedValueException('Unsupported SSL DNS record type.');
            }
            return new DnsRecord($type, DataReader::nullableString($record['name'] ?? null, 'dns_records.ssl[].name'), DataReader::nullableString($record['value'] ?? null, 'dns_records.ssl[].value'));
        }, DataReader::list($value['ssl'] ?? null, 'dns_records.ssl'));
        return new DomainDnsRecords($ssl, DataReader::string($value['pre_verification'] ?? null, 'dns_records.pre_verification'), DataReader::string($value['origin'] ?? null, 'dns_records.origin'), DataReader::string($value['origin_cname'] ?? null, 'dns_records.origin_cname'), DataReader::string($value['dcv'] ?? null, 'dns_records.dcv'));
    }

    /** @param array<string, mixed> $value */
    private function variant(array $value): DomainVariant
    {
        return new DomainVariant(DomainStatus::from(DataReader::string($value['hostname_status'] ?? null, 'variant.hostname_status')), DomainStatus::from(DataReader::string($value['ssl_status'] ?? null, 'variant.ssl_status')), DomainStatus::from(DataReader::string($value['origin_status'] ?? null, 'variant.origin_status')), $this->dnsRecords(DataReader::object($value['dns_records'] ?? null, 'variant.dns_records')));
    }

    /** @param array<string, mixed> $resource */
    private function environmentId(array $resource): ?string
    {
        $relationships = DataReader::optionalObject($resource, 'relationships', 'domain.relationships');
        if (null === $relationships || !array_key_exists('environment', $relationships)) {
            return null;
        }
        $relationship = DataReader::object($relationships['environment'], 'relationships.environment');
        if (null === ($relationship['data'] ?? null)) {
            return null;
        }
        $identifier = DataReader::object($relationship['data'], 'relationships.environment.data');
        if ('environments' !== DataReader::string($identifier['type'] ?? null, 'relationships.environment.data.type')) {
            throw new UnexpectedValueException('Expected environments relationship.');
        }
        return DataReader::string($identifier['id'] ?? null, 'relationships.environment.data.id');
    }

    /** @param array<string, mixed> $resource */
    private function selfLink(array $resource): Link
    {
        $links = DataReader::object($resource['links'] ?? null, 'domain.links');
        $self = DataReader::object($links['self'] ?? null, 'domain.links.self');
        return new Link(DataReader::string($self['href'] ?? null, 'domain.links.self.href'), DataReader::nullableString($self['rel'] ?? null, 'link.rel'), DataReader::nullableString($self['describedby'] ?? null, 'link.describedby'), DataReader::nullableString($self['title'] ?? null, 'link.title'), DataReader::nullableString($self['type'] ?? null, 'link.type'), null, array_key_exists('meta', $self) ? DataReader::object($self['meta'], 'link.meta') : null);
    }

    /** @return list<EnvironmentResource> */
    private function included(mixed $value): array
    {
        return array_map(static function (object $resource): EnvironmentResource {
            if (!$resource instanceof EnvironmentResource) {
                throw new UnexpectedValueException('Domains may only include environment resources.');
            }
            return $resource;
        }, $this->applicationsHydrator->hydrateIncluded($value));
    }

    /** @param array<string, mixed> $value */
    private function paginationLinks(array $value): PaginationLinks
    {
        return new PaginationLinks(DataReader::nullableString($value['first'] ?? null, 'links.first'), DataReader::nullableString($value['last'] ?? null, 'links.last'), DataReader::nullableString($value['prev'] ?? null, 'links.prev'), DataReader::nullableString($value['next'] ?? null, 'links.next'));
    }

    /** @param array<string, mixed> $value */
    private function paginationMeta(array $value): PaginationMeta
    {
        $links = array_map(static function (mixed $item): PaginatorLink {
            $link = DataReader::object($item, 'meta.links[]');
            return new PaginatorLink(DataReader::nullableString($link['url'] ?? null, 'meta.links[].url'), DataReader::string($link['label'] ?? null, 'meta.links[].label'), DataReader::bool($link['active'] ?? null, 'meta.links[].active'));
        }, DataReader::list($value['links'] ?? null, 'meta.links'));
        return new PaginationMeta(DataReader::int($value['current_page'] ?? null, 'meta.current_page'), DataReader::nullableInt($value['from'] ?? null, 'meta.from'), DataReader::int($value['last_page'] ?? null, 'meta.last_page'), $links, DataReader::nullableString($value['path'] ?? null, 'meta.path'), DataReader::int($value['per_page'] ?? null, 'meta.per_page'), DataReader::nullableInt($value['to'] ?? null, 'meta.to'), DataReader::int($value['total'] ?? null, 'meta.total'));
    }

    /** @return array<string, mixed> */
    private function resource(mixed $value): array
    {
        $resource = DataReader::object($value, 'domain');
        if ('domains' !== DataReader::string($resource['type'] ?? null, 'domain.type')) {
            throw new UnexpectedValueException('Expected domains resource type.');
        }
        return $resource;
    }

    private function nullableBool(mixed $value, string $path): ?bool
    {
        return null === $value ? null : DataReader::bool($value, $path);
    }
}
