<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Resources\Commands;

use CommunitySDKs\LaravelCloud\Enums\Commands\CommandStatus;
use DateTimeImmutable;

/** Command execution details. */ final readonly class CommandAttributes
{
    public function __construct(public string $command, public ?string $output, public CommandStatus $status, public ?int $exitCode, public ?string $failureReason, public ?DateTimeImmutable $startedAt, public ?DateTimeImmutable $finishedAt, public ?DateTimeImmutable $createdAt) {}
}
