<?php

namespace App\Services\AI\Gemini;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Validators\TicketAnalysisResponseValidator;

class GeminiResponseParser implements AIResponseParser
{
    public function __construct(protected TicketAnalysisResponseValidator $validator) {}

    public function parse(array $rawResponse): TicketAnalysisData
    {
        $text = data_get($rawResponse, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new InvalidAIResponseException(sprintf(
                'Gemini response contained no text (finish reason: %s).',
                data_get($rawResponse, 'candidates.0.finishReason', 'unknown')
            ));
        }

        $result = json_decode($this->stripCodeFences($text), true);

        if (! is_array($result)) {
            throw new InvalidAIResponseException(
                'Gemini returned invalid JSON: '.json_last_error_msg()
            );
        }

        $result = $this->validator->validate($result);

        return new TicketAnalysisData(
            category: $result['category'],
            priority: $result['priority'],
            summary: $result['summary'],
            suggestedReply: $result['suggested_reply'],
        );
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
