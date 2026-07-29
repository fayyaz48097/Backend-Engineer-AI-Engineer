<?php

namespace App\Services\AI\Factories;

use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Fakes\FakeAIClient;
use App\Services\AI\Gemini\GeminiClient;
use InvalidArgumentException;

class AIClientFactory
{
    public function make(): AIClient
    {
        return match (config('ai.provider')) {

            'gemini' => app(GeminiClient::class),

            'fake' => app(FakeAIClient::class),

            default => throw new InvalidArgumentException(
                'Unsupported AI provider.'
            ),
        };
    }
}