<?php

namespace App\Services\AI\Validators;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TicketAnalysisResponseValidator
{
    /**
     * Treat the model's output as untrusted input: normalise what is safe to
     * normalise, reject everything else.
     *
     * @throws InvalidAIResponseException
     */
    public function validate(array $data): array
    {
        $data = $this->normalize($data);

        try {
            return Validator::make($data, [
                'category' => [
                    'required',
                    'string',
                    'in:'.implode(',', TicketAnalysisData::CATEGORIES),
                ],
                'priority' => [
                    'required',
                    'string',
                    'in:'.implode(',', TicketAnalysisData::PRIORITIES),
                ],
                'summary' => [
                    'required',
                    'string',
                ],
                'suggested_reply' => [
                    'required',
                    'string',
                ],
            ])->validate();
        } catch (ValidationException $e) {
            // Keep every AI failure inside the AIException hierarchy so callers
            // only ever have to reason about one family of errors.
            throw new InvalidAIResponseException(
                'AI response failed validation: '.implode(' ', $e->validator->errors()->all()),
                previous: $e
            );
        }
    }

    /**
     * The model reliably returns the right word but not always the right case
     * ("technical" vs "Technical"). That is a formatting slip, not a wrong
     * answer, so map it back onto the canonical value.
     */
    private function normalize(array $data): array
    {
        $data['category'] = $this->canonical($data['category'] ?? null, TicketAnalysisData::CATEGORIES);
        $data['priority'] = $this->canonical($data['priority'] ?? null, TicketAnalysisData::PRIORITIES);

        foreach (['summary', 'suggested_reply'] as $key) {
            if (is_string($data[$key] ?? null)) {
                $data[$key] = trim($data[$key]);
            }
        }

        return $data;
    }

    private function canonical(mixed $value, array $allowed): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        foreach ($allowed as $candidate) {
            if (strcasecmp(trim($value), $candidate) === 0) {
                return $candidate;
            }
        }

        return $value;
    }
}
