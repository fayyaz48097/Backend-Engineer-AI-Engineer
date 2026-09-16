<?php

namespace App\Services\AI\Validators;

use App\Services\AI\Exceptions\InvalidAIResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GeneratedEmailValidator
{
    /**
     * Unfilled placeholders are the most common way an LLM-written email
     * embarrasses you in front of a customer, and they are trivially
     * detectable, so they are a hard failure rather than a warning.
     */
    private const PLACEHOLDER_PATTERNS = [
        '/\{\{.*?\}\}/',
        '/\[(insert|your|customer|client|company|name|date|amount)\b[^\]]*\]/i',
        '/\bTODO\b/i',
        '/\bXXXX+\b/',
    ];

    /**
     * @throws InvalidAIResponseException
     */
    public function validate(array $data): array
    {
        $data = $this->normalize($data);

        try {
            $validated = Validator::make($data, [
                'subject' => ['required', 'string', 'max:120'],
                'body' => [
                    'required',
                    'string',
                    'min:20',
                    'max:'.(int) config('ai.email.max_body_length', 5000),
                ],
            ])->validate();
        } catch (ValidationException $e) {
            throw new InvalidAIResponseException(
                'Generated email failed validation: '.implode(' ', $e->validator->errors()->all()),
                previous: $e
            );
        }

        $this->rejectPlaceholders($validated);

        return $validated;
    }

    private function normalize(array $data): array
    {
        foreach (['subject', 'body'] as $key) {
            if (is_string($data[$key] ?? null)) {
                $data[$key] = trim($data[$key]);
            }
        }

        // A subject line must never span lines: a newline in a mail header is
        // a header-injection vector, not merely untidy.
        if (is_string($data['subject'] ?? null)) {
            $data['subject'] = preg_replace('/[\r\n]+/', ' ', $data['subject']);
        }

        return $data;
    }

    private function rejectPlaceholders(array $data): void
    {
        foreach (self::PLACEHOLDER_PATTERNS as $pattern) {
            foreach (['subject', 'body'] as $field) {
                if (preg_match($pattern, $data[$field], $matches)) {
                    throw new InvalidAIResponseException(sprintf(
                        'Generated email contains an unfilled placeholder in %s: "%s".',
                        $field,
                        $matches[0]
                    ));
                }
            }
        }
    }
}
