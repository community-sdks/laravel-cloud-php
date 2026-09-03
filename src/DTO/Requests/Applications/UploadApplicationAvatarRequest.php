<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Applications;

use InvalidArgumentException;

/** Binary avatar content and optional multipart filename for an application. */
final readonly class UploadApplicationAvatarRequest
{
    public const MAXIMUM_BYTES = 3072 * 1024;

    public function __construct(
        public string $avatar,
        public string $filename = 'avatar',
    ) {
        if ('' === $avatar) {
            throw new InvalidArgumentException('Application avatar content cannot be empty.');
        }
        if (strlen($avatar) > self::MAXIMUM_BYTES) {
            throw new InvalidArgumentException('Application avatar cannot exceed 3072 kilobytes.');
        }
        if ('' === trim($filename)) {
            throw new InvalidArgumentException('Application avatar filename cannot be empty.');
        }
    }
}
