<?php

namespace App\Services\AI\Contracts;

interface AIPromptBuilder
{
    /**
     * Build the final prompt string to send to the AI.
     */
    public function build(string $subject, string $message): string;

    /**
     * Describe the JSON shape the model must return.
     *
     * Provider-neutral JSON Schema; each AIClient translates it into
     * whatever its own API calls structured output. Return an empty
     * array to fall back to prompt-only instructions.
     */
    public function responseSchema(): array;
}
