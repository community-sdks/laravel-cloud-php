<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

/** Optional redeploy behavior when starting an environment. */
final readonly class StartEnvironmentRequest
{
    public function __construct(public ?bool $redeploy = null) {} /** @return array{redeploy:?bool} */ public function toArray(): array
    {
        return ['redeploy' => $this->redeploy];
    }
}
