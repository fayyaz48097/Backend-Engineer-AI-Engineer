<?php

namespace App\Data\AI;

final readonly class TicketAnalysisData
{
    /**
     * The single source of truth for the analysis contract.
     *
     * These lists are consumed by the prompt, the provider response schema
     * and the response validator, so the three can never drift apart.
     */
    public const CATEGORIES = ['Technical', 'Billing', 'General', 'Sales'];

    public const PRIORITIES = ['Low', 'Medium', 'High'];

    public function __construct(
        public string $category,
        public string $priority,
        public string $summary,
        public string $suggestedReply,
    ) {
    }

    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'priority' => $this->priority,
            'summary' => $this->summary,
            'suggested_reply' => $this->suggestedReply,
        ];
    }
}