<?php

namespace App\Services\AI\Fakes;

use App\Services\AI\Contracts\AIClient;

/**
 * Deterministic client for local development and tests. It mimics the Gemini
 * envelope so the real parser is exercised end to end without a network call.
 */
class FakeAIClient implements AIClient
{
    public function generate(string $prompt, array $responseSchema = []): array
    {
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'category' => 'Technical',
                                    'priority' => 'High',
                                    'summary' => 'This is a fake AI response.',
                                    'suggested_reply' => 'This response came from FakeAIClient.',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
