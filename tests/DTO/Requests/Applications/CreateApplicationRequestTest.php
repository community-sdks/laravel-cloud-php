<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Tests\DTO\Requests\Applications;

use CommunitySDKs\LaravelCloud\DTO\Requests\Applications\CreateApplicationRequest;
use CommunitySDKs\LaravelCloud\Enums\Applications\CloudRegion;
use CommunitySDKs\LaravelCloud\Enums\Applications\SourceControlProviderType;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Verifies local constraints and wire serialization for application creation. */
final class CreateApplicationRequestTest extends TestCase
{
    public function test_it_serializes_documented_snake_case_fields(): void
    {
        $request = new CreateApplicationRequest(
            SourceControlProviderType::GitLab,
            'acme/project',
            'Acme Project',
            CloudRegion::CanadaCentral1,
        );

        self::assertSame([
            'source_control_provider_type' => 'gitlab',
            'repository' => 'acme/project',
            'name' => 'Acme Project',
            'region' => 'ca-central-1',
            'root_directory' => null,
            'cluster_id' => null,
        ], $request->toArray());
    }

    /** @return iterable<string, array{string}> */
    public static function invalidNames(): iterable
    {
        yield 'too short' => ['ab'];
        yield 'unsupported character' => ['invalid/name'];
        yield 'too long' => [str_repeat('a', 41)];
    }

    #[DataProvider('invalidNames')]
    public function test_it_rejects_invalid_application_names(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateApplicationRequest(
            SourceControlProviderType::Bitbucket,
            'acme/project',
            $name,
            CloudRegion::UsEast2,
        );
    }

    public function test_it_rejects_an_invalid_root_directory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateApplicationRequest(
            SourceControlProviderType::GitHub,
            'acme/project',
            'Acme Project',
            CloudRegion::UsEast2,
            '/absolute/path',
        );
    }
}
