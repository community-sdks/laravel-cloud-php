<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Applications\NodeVersion;
use CommunitySDKs\LaravelCloud\Enums\Environments\CacheStrategy;
use CommunitySDKs\LaravelCloud\Enums\Environments\ContentTypePolicy;
use CommunitySDKs\LaravelCloud\Enums\Environments\EnvironmentColor;
use CommunitySDKs\LaravelCloud\Enums\Environments\FramePolicy;
use CommunitySDKs\LaravelCloud\Enums\Environments\PhpVersion;
use CommunitySDKs\LaravelCloud\Enums\Environments\RobotsPolicy;
use InvalidArgumentException;

/** Optional documented fields for updating an environment. */
final readonly class UpdateEnvironmentRequest
{
    /** @param list<FilesystemKeyInput>|null $filesystemKeys */
    public function __construct(public ?string $name = null, public ?string $slug = null, public ?EnvironmentColor $color = null, public ?string $branch = null, public ?bool $usesPushToDeploy = null, public ?bool $usesDeployHook = null, public ?int $timeout = null, public ?PhpVersion $phpVersion = null, public ?string $buildCommand = null, public ?NodeVersion $nodeVersion = null, public ?string $deployCommand = null, public ?bool $usesVanityDomain = null, public ?string $databaseSchemaId = null, public ?string $cacheId = null, public ?string $websocketApplicationId = null, public ?array $filesystemKeys = null, public ?bool $usesOctane = null, public ?int $sleepTimeout = null, public ?int $hibernationWakeUpInterval = null, public ?int $shutdownTimeout = null, public ?bool $usesPurgeEdgeCacheOnDeploy = null, public ?string $nightwatchToken = null, public ?CacheStrategy $cacheStrategy = null, public ?FramePolicy $responseHeadersFrame = null, public ?ContentTypePolicy $responseHeadersContentType = null, public ?RobotsPolicy $responseHeadersRobotsTag = null, public ?HstsInput $responseHeadersHsts = null, public ?bool $firewallBlockPath = null, public ?bool $firewallBrowserIntegrityCheck = null)
    {
        if (null !== $name && 1 !== preg_match('/^[A-Za-z0-9 _-]{1,40}$/', $name)) {
            throw new InvalidArgumentException('Invalid environment name.');
        }if (null !== $timeout && ($timeout < 5 || $timeout > 60)) {
            throw new InvalidArgumentException('Timeout must be 5–60.');
        }
    }
    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $map = ['name' => $this->name,'slug' => $this->slug,'color' => $this->color?->value,'branch' => $this->branch,'uses_push_to_deploy' => $this->usesPushToDeploy,'uses_deploy_hook' => $this->usesDeployHook,'timeout' => $this->timeout,'php_version' => $this->phpVersion?->value,'build_command' => $this->buildCommand,'node_version' => $this->nodeVersion?->value,'deploy_command' => $this->deployCommand,'uses_vanity_domain' => $this->usesVanityDomain,'database_schema_id' => $this->databaseSchemaId,'cache_id' => $this->cacheId,'websocket_application_id' => $this->websocketApplicationId,'filesystem_keys' => null === $this->filesystemKeys ? null : array_map(static fn(FilesystemKeyInput $v): array => $v->toArray(), $this->filesystemKeys),'uses_octane' => $this->usesOctane,'sleep_timeout' => $this->sleepTimeout,'hibernation_wake_up_interval' => $this->hibernationWakeUpInterval,'shutdown_timeout' => $this->shutdownTimeout,'uses_purge_edge_cache_on_deploy' => $this->usesPurgeEdgeCacheOnDeploy,'nightwatch_token' => $this->nightwatchToken,'cache_strategy' => $this->cacheStrategy?->value,'response_headers_frame' => $this->responseHeadersFrame?->value,'response_headers_content_type' => $this->responseHeadersContentType?->value,'response_headers_robots_tag' => $this->responseHeadersRobotsTag?->value,'response_headers_hsts' => $this->responseHeadersHsts?->toArray(),'firewall_block_path' => $this->firewallBlockPath,'firewall_browser_integrity_check' => $this->firewallBrowserIntegrityCheck];
        return array_filter($map,static fn(mixed $v): bool => null !== $v);
    }
}
