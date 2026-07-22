<?php

namespace App\Services\AI\Gemini;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Validators\TicketAnalysisResponseValidator;

class GeminiResponseParser implements AIResponseParser
{
    public function __construct(protected TicketAnalysisResponseValidator $validator)
    {

    }
    public function parse(array $rawResponse): TicketAnalysisData
    {
        $text = data_get($rawResponse, 'candidates.0.content.parts.0.text');

        $text = trim(
            str_replace(
                ['```json', '```'],
                '',
                $text
            )
        );

        $result = json_decode($text, true);

        if (!is_array($result)) {
            throw new \RuntimeException('Invalid AI JSON response.');
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