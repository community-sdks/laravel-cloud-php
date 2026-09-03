<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Responses\Environments;

use CommunitySDKs\LaravelCloud\DTO\Resources\Environments\LogResource;

/** Cursor-paginated normalized environment logs. */
final readonly class ListEnvironmentLogsResponse
{ /** @param list<LogResource> $data */ public function __construct(public array $data, public string $cursor, public string $type, public string $from, public string $to) {}
}
