<?php

namespace App\Services\AI;

use App\Data\AI\TicketAnalysisData;
use App\Models\Ticket;
use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Contracts\AIPromptBuilder;
use App\Services\AI\Contracts\AIResponseParser;

class TicketAnalyzer
{
    public function __construct(
        protected AIClient $client,
        protected AIPromptBuilder $promptBuilder,
        protected AIResponseParser $responseParser,
    ) {}

    public function analyzeTicket(Ticket $ticket): TicketAnalysisData
    {
        $prompt = $this->promptBuilder->build(
            subject: $ticket->subject,
            message: $ticket->message,
        );

        $rawResponse = $this->client->generate(
            prompt: $prompt,
            responseSchema: $this->promptBuilder->responseSchema(),
        );

        return $this->responseParser->parse($rawResponse);
    }
}
