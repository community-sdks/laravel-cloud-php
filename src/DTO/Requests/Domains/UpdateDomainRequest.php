<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainVerificationMethod;

/** Required verification method used to update a domain. */
final readonly class UpdateDomainRequest
{
    public function __construct(public DomainVerificationMethod $verificationMethod) {}
    /** @return array{verification_method: string} */
    public function toArray(): array
    {
        return ['verification_method' => $this->verificationMethod->value];
    }
}
