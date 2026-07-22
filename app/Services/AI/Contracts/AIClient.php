<?php

namespace App\Services\AI\Contracts;

interface AIClient
{
    /**
     * Send a prompt to the AI provider and return the raw response array.
     */
    public function generate(string $prompt): array;
}