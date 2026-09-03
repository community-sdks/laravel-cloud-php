<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\LogLevel;
use CommunitySDKs\LaravelCloud\Enums\Environments\NormalizedLogType;

/** One normalized log event; data varies by the documented log type. */
final readonly class LogResource
{ /** @param array<string,mixed>|null $data */ public function __construct(public string $message, public LogLevel $level, public NormalizedLogType $type, public string $loggedAt, public ?array $data) {}
}
