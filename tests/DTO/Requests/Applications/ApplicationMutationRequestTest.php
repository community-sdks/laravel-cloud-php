<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UpdateApplicationRequest;
use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\UploadApplicationAvatarRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** Verifies update omission semantics and avatar size constraints. */
final class ApplicationMutationRequestTest extends TestCase
{
    public function test_update_omits_unspecified_fields_and_can_explicitly_clear_slack(): void
    {
        self::assertSame([], (new UpdateApplicationRequest())->toArray());
        self::assertSame(
            ['slack_channel' => null],
            (new UpdateApplicationRequest(clearSlackChannel: true))->toArray(),
        );
    }

    public function test_avatar_rejects_content_over_the_documented_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UploadApplicationAvatarRequest(str_repeat('x', UploadApplicationAvatarRequest::MAXIMUM_BYTES + 1));
    }
}
