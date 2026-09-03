<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use InvalidArgumentException;

/** Object-storage attachment entry for an environment update. */
final readonly class FilesystemKeyInput
{
    public function __construct(public string $id, public string $disk, public bool $isDefaultDisk)
    {
        if (1 !== preg_match('/^[A-Za-z0-9 _-]{1,40}$/', $disk)) {
            throw new InvalidArgumentException('Filesystem disk must be 1–40 supported characters.');
        }
    } /** @return array{id:string,disk:string,is_default_disk:bool} */ public function toArray(): array
    {
        return ['id' => $this->id,'disk' => $this->disk,'is_default_disk' => $this->isDefaultDisk];
    }
}
