<?php

namespace App\Services\AI\Gemini;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Contracts\AIJsonExtractor;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Validators\TicketAnalysisResponseValidator;

class GeminiResponseParser implements AIResponseParser
{
    public function __construct(
        protected AIJsonExtractor $extractor,
        protected TicketAnalysisResponseValidator $validator,
    ) {}

    public function parse(array $rawResponse): TicketAnalysisData
    {
        $result = $this->validator->validate(
            $this->extractor->extract($rawResponse)
        );

        return new TicketAnalysisData(
            category: $result['category'],
            priority: $result['priority'],
            summary: $result['summary'],
            suggestedReply: $result['suggested_reply'],
        );
    }
}
