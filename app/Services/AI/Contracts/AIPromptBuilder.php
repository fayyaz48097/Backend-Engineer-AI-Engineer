<?php

namespace App\Services\AI\Contracts;

interface AIPromptBuilder
{
    /**
     * Build the final prompt string to send to the AI.
     */
    public function build(string $subject, string $message): string;
}