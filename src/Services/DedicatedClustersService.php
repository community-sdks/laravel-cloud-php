<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

/**
 * Provides the extension point for documented dedicated cluster operations.
 *
 * Endpoint methods are added only when their official Laravel Cloud schema is
 * available, keeping the public API strongly typed and contract-accurate.
 */
final class DedicatedClustersService extends AbstractService {}
