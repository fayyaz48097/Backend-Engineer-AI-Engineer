<?php

namespace App\Data\AI;

final readonly class TicketAnalysisData
{
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