<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Commands;

use InvalidArgumentException;

/** Command text to execute remotely. */ final readonly class CreateCommandRequest
{
    public function __construct(public string $command)
    {
        $n = strlen($command);
        if ($n < 1 || $n > 150000) {
            throw new InvalidArgumentException('Command must contain 1–150000 characters.');
        }
    } /** @return array{command:string} */ public function toArray(): array
    {
        return['command' => $this->command];
    }
}
