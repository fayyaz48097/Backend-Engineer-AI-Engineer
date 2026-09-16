<?php

namespace App\Services\AI\Contracts;

use App\Services\AI\Exceptions\InvalidAIResponseException;

interface AIJsonExtractor
{
    /**
     * Pull the model's JSON answer out of a provider-specific envelope.
     *
     * Every provider wraps the answer differently, but every AI feature in
     * this app wants the same thing out of it: a decoded array. Keeping that
     * step in one place is what lets a second feature (email generation)
     * reuse the first feature's provider plumbing.
     *
     * @throws InvalidAIResponseException
     */
    public function extract(array $rawResponse): array;
}
