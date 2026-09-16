<?php

namespace App\Services\AI\Fakes;

use App\Services\AI\Contracts\AIClient;

/**
 * Deterministic client for local development and tests. It mimics the Gemini
 * envelope so the real extractor, validator and parser are exercised end to
 * end without a network call, and answers according to the schema it is
 * handed so it can stand in for every AI feature in the app.
 */
class FakeAIClient implements AIClient
{
    public function generate(string $prompt, array $responseSchema = []): array
    {
        return $this->envelope($this->answerFor($responseSchema));
    }

    private function answerFor(array $responseSchema): array
    {
        $properties = $responseSchema['properties'] ?? [];

        return match (true) {
            isset($properties['body']) => [
                'subject' => 'Regarding your support request',
                'body' => "Dear Customer,\n\nThank you for contacting us. "
                    ."This email was produced by FakeAIClient for testing.\n\n"
                    .'The Support Team',
            ],
            default => [
                'category' => 'Technical',
                'priority' => 'High',
                'summary' => 'This is a fake AI response.',
                'suggested_reply' => 'This response came from FakeAIClient.',
            ],
        };
    }

    private function envelope(array $answer): array
    {
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($answer)],
                        ],
                    ],
                ],
            ],
        ];
    }
}
