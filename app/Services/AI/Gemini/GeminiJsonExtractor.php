<?php

namespace App\Services\AI\Gemini;

use App\Services\AI\Contracts\AIJsonExtractor;
use App\Services\AI\Exceptions\InvalidAIResponseException;

class GeminiJsonExtractor implements AIJsonExtractor
{
    public function extract(array $rawResponse): array
    {
        $text = data_get($rawResponse, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new InvalidAIResponseException(sprintf(
                'Gemini response contained no text (finish reason: %s).',
                data_get($rawResponse, 'candidates.0.finishReason', 'unknown')
            ));
        }

        $decoded = json_decode($this->stripCodeFences($text), true);

        if (! is_array($decoded)) {
            throw new InvalidAIResponseException(
                'Gemini returned invalid JSON: '.json_last_error_msg()
            );
        }

        return $decoded;
    }

    /**
     * With a responseSchema in play Gemini returns bare JSON, so this is
     * belt-and-braces for the schema-less path and for older models that
     * still wrap their answer in a markdown fence.
     */
    private function stripCodeFences(string $text): string
    {
        return trim(preg_replace('/^```(?:json)?|```$/m', '', trim($text)));
    }
}
