<?php

namespace App\Services\AI\Contracts;

interface AIClient
{
    /**
     * Send a prompt to the AI provider and return the raw response array.
     *
     * When $responseSchema is not empty the provider is asked to constrain
     * its output to that shape instead of merely being asked nicely.
     */
    public function generate(string $prompt, array $responseSchema = []): array;
}
