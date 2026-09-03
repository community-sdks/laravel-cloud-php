<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Commands;

use CommunitySDKs\LaravelCloud\DTO\Resources\Commands\CommandResource;

/** Single command response. */ final readonly class CommandResponse
{
    public function __construct(public CommandResource $data) {}
}
