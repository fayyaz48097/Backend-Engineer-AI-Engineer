<?php

namespace App\Services\AI\Fakes;

use App\Services\AI\Contracts\AIClient;

class FakeAIClient implements AIClient
{
    public function generate(string $prompt): array
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