<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Exceptions;

use JsonException;

/** Thrown when Laravel Cloud rejects request data as invalid (HTTP 422). */
final class ValidationException extends ApiException
{
    /**
     * Return validation messages grouped by request field.
     *
     * @return array<string, list<string>>
     */
    public function getErrors(): array
    {
        try {
            $payload = json_decode($this->getResponseBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        if (!is_array($payload) || !isset($payload['errors']) || !is_array($payload['errors'])) {
            return [];
        }

        $errors = [];
        foreach ($payload['errors'] as $field => $messages) {
            if (!is_string($field) || !is_array($messages) || !array_is_list($messages)) {
                continue;
            }

            $typedMessages = array_values(array_filter($messages, is_string(...)));
            $errors[$field] = $typedMessages;
        }

        return $errors;
    }
}
