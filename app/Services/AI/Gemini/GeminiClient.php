<?php

namespace App\Services\AI\Gemini;

use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Exceptions\AIConnectionException;
use App\Services\AI\Exceptions\AIRateLimitException;
use App\Services\AI\Exceptions\AIResponseException;
use Illuminate\Support\Facades\Http;

class GeminiClient implements AIClient
{
    public function generate(string $prompt): array
    {
        try {
            $response = retry(
                config('ai.retry.times'),
                function () use ($prompt) {
                    return Http::timeout(config('ai.timeout'))->post(
                        sprintf(
                            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                            config('ai.providers.gemini.model'),
                            config('services.gemini.api_key')
                        ),
                        [
                            'contents' => [
                                'parts' => [['text' => $prompt]],
                            ],
                        ]
                    );
                },
                function ($attempt) {
                    return pow(2, $attempt) * 1000;
                }
            );
        } catch (\Throwable $e) {
            throw new AIConnectionException(
                'Unable to connect to Gemini API.',
                previous: $e
            );
        }
        if ($response->status() === 429) {
            throw new AIRateLimitException(
                'Gemini rate limit exceeded.'
            );
        }
        if ($response->failed()) {
            throw new AIResponseException(
                'Gemini API request failed.'
            );
        }

        return $response->json();
    }
}