<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\DTO\Requests\Environments;

use CommunitySDKs\LaravelCloud\Enums\Environments\LogType;
use DateTimeInterface;

/** Required log time range with optional search and cursor filters. */
final readonly class ListEnvironmentLogsRequest
{
    /** @param list<string> $instances */
    public function __construct(public DateTimeInterface $from, public DateTimeInterface $to, public ?string $query = null, public ?LogType $type = null, public ?bool $caseSensitive = null, public ?bool $wholeWord = null, public array $instances = [], public ?string $cursor = null) {}
    /** @return array<string,string|bool|list<string>> */
    public function toQuery(): array
    {
        $q = ['from' => $this->from->format(DATE_ATOM),'to' => $this->to->format(DATE_ATOM)];
        foreach (['query' => $this->query,'type' => $this->type?->value,'case_sensitive' => $this->caseSensitive,'whole_word' => $this->wholeWord,'cursor' => $this->cursor] as $k => $v) {
            if (null !== $v) {
                $q[$k] = $v;
            }
        }if ([] !== $this->instances) {
            $q['instances[]'] = $this->instances;
        }return$q;
    }
}
