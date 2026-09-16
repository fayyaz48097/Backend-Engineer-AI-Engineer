<?php

namespace App\Services\AI\Factories;

use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Contracts\AIJsonExtractor;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Fakes\FakeAIClient;
use App\Services\AI\Gemini\GeminiClient;
use App\Services\AI\Gemini\GeminiJsonExtractor;
use App\Services\AI\Gemini\GeminiResponseParser;
use InvalidArgumentException;

class AIClientFactory
{
    /**
     * provider => [client, ticket analysis parser, json extractor]
     *
     * A client and the classes that understand its response envelope are a
     * set; resolving them separately is how you end up asking the Gemini
     * parser to read an OpenAI response. Adding a provider means adding one
     * row here plus the classes it points at.
     *
     * The fake deliberately reuses the Gemini readers because it returns a
     * Gemini-shaped envelope.
     */
    private const PROVIDERS = [
        'gemini' => [GeminiClient::class, GeminiResponseParser::class, GeminiJsonExtractor::class],
        'fake' => [FakeAIClient::class, GeminiResponseParser::class, GeminiJsonExtractor::class],
    ];

    public function client(): AIClient
    {
        return app($this->resolve()[0]);
    }

    public function parser(): AIResponseParser
    {
        return app($this->resolve()[1]);
    }

    public function jsonExtractor(): AIJsonExtractor
    {
        return app($this->resolve()[2]);
    }

    private function resolve(): array
    {
        $provider = config('ai.provider');

        return self::PROVIDERS[$provider] ?? throw new InvalidArgumentException(sprintf(
            'Unsupported AI provider [%s]. Supported: %s.',
            (string) $provider,
            implode(', ', array_keys(self::PROVIDERS))
        ));
    }
}
