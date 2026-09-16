<?php

namespace App\Services\AI\Email;

use App\Data\AI\GeneratedEmailData;
use App\Models\Ticket;
use App\Services\AI\Contracts\AIClient;

/**
 * Mirrors TicketAnalyzer: build a prompt, call the provider, parse the answer.
 * It reuses the same AIClient, so provider selection, retries, timeouts and
 * structured output all behave identically to ticket analysis.
 */
class EmailGenerator
{
    public function __construct(
        protected AIClient $client,
        protected EmailPromptBuilder $promptBuilder,
        protected EmailResponseParser $responseParser,
    ) {}

    public function generate(Ticket $ticket): GeneratedEmailData
    {
        $rawResponse = $this->client->generate(
            prompt: $this->promptBuilder->build($ticket),
            responseSchema: $this->promptBuilder->responseSchema(),
        );

        return $this->responseParser->parse($rawResponse);
    }
}
