<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Domains;

use CommunitySDKs\LaravelCloud\Enums\Domains\DomainCloudflareStrategy;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainRedirect;
use CommunitySDKs\LaravelCloud\Enums\Domains\DomainVerificationMethod;
use InvalidArgumentException;

/** Request body for attaching a domain to an environment. */
final readonly class CreateDomainRequest
{
    public function __construct(public string $name, public ?DomainRedirect $wwwRedirect = null, public ?bool $wildcardEnabled = null, public ?bool $allowDowntime = null, public ?DomainCloudflareStrategy $cloudflareStrategy = null, public ?DomainVerificationMethod $verificationMethod = null)
    {
        $length = strlen($name);
        if ($length < 3 || $length > 255) {
            throw new InvalidArgumentException('Domain name must contain between 3 and 255 characters.');
        }
    }
    /** @return array<string, string|bool|null> */
    public function toArray(): array
    {
        return ['name' => $this->name, 'www_redirect' => $this->wwwRedirect?->value, 'wildcard_enabled' => $this->wildcardEnabled, 'allow_downtime' => $this->allowDowntime, 'cloudflare_strategy' => $this->cloudflareStrategy?->value, 'verification_method' => $this->verificationMethod?->value];
    }
}
