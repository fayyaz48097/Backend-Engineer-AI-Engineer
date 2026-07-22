<?php

namespace App\Services\AI\Gemini;

use App\Services\AI\Contracts\AIClient;
use Illuminate\Support\Facades\Http;

class GeminiClient implements AIClient
{
    public function generate(string $prompt): array
    {
        $response = Http::timeout(30)->post(
            sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                config('services.gemini.model'),
                config('services.gemini.api_key')
            ),
            [
                'contents' => [
                    'parts' => [['text' => $prompt]],
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Gemini API request failed.');
        }

        return $response->json();
    }
}