<?php

namespace App\Services\AI\Gemini;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Validators\TicketAnalysisResponseValidator;

class GeminiResponseParser implements AIResponseParser
{
    public function __construct(protected TicketAnalysisResponseValidator $validator)
    {

    }
    public function parse(array $rawResponse): TicketAnalysisData
    {
        $text = data_get($rawResponse, 'candidates.0.content.parts.0.text');
        if (!$text) {
            throw new InvalidAIResponseException(
                'Gemini response text is missing.'
            );
        }
        $text = trim(
            str_replace(
                ['```json', '```'],
                '',
                $text
            )
        );

        $result = json_decode($text, true);
        if (!is_array($result)) {
            throw new InvalidAIResponseException(
                'Gemini returned invalid JSON.'
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
}