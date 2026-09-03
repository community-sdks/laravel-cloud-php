<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Applications;

use CommunitySDKs\LaravelCloud\Enums\Applications\BotControlCategory;

/** Firewall and automated-bot controls for an environment. */
final readonly class FirewallSettings
{
    /** @param list<BotControlCategory> $botCategories */
    public function __construct(
        public array $botCategories,
        public RateLimitSettings $rateLimit,
        public string $underAttackModeStartedAt,
        public bool $blockPath,
    ) {}
}
