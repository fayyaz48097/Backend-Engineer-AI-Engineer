<?php

namespace App\Data\AI;

final readonly class GeneratedEmailData
{
    public function __construct(
        public string $subject,
        public string $body,
    ) {}

    /**
     * Keyed for a direct Ticket::update().
     */
    public function toArray(): array
    {
        return [
            'email_subject' => $this->subject,
            'email_body' => $this->body,
        ];
    }
}
