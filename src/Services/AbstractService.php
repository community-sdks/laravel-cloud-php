<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Services;

use CommunitySDKs\LaravelCloud\Http\ApiTransport;

/**
 * Shares the authenticated HTTP transport with every API service.
 *
 * Concrete services inherit this constructor and use the transport only from
 * documented endpoint methods, keeping HTTP concerns out of public DTOs.
 */
abstract class AbstractService
{
    public function __construct(
        protected readonly ApiTransport $transport,
    ) {}
}
