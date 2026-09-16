<?php

namespace App\Services\AI\Gemini;

use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Exceptions\AIConnectionException;
use App\Services\AI\Exceptions\AIRateLimitException;
use App\Services\AI\Exceptions\AIResponseException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GeminiClient implements AIClient
{
    /**
     * Status codes worth trying again. Anything else is deterministic:
     * retrying a 400 just buys the same 400 three times.
     */
    private const RETRYABLE_STATUSES = [408, 429, 500, 502, 503, 504];

    public function generate(string $prompt, array $responseSchema = []): array
    {
        $model = config('ai.providers.gemini.model');

        try {
            $response = Http::timeout(config('ai.timeout'))
                ->connectTimeout(config('ai.connect_timeout'))
                // The API key travels as a header, not a query string, so it
                // never lands in proxy or web-server access logs.
                ->withHeader('x-goog-api-key', config('services.gemini.api_key'))
                ->retry(
                    times: config('ai.retry.times'),
                    sleepMilliseconds: fn (int $attempt) => (2 ** $attempt) * config('ai.retry.delay'),
                    when: fn (\Throwable $e) => $this->shouldRetry($e),
                    throw: false,
                )
                ->post(
                    sprintf(
                        'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
                        $model
                    ),
                    $this->payload($prompt, $responseSchema)
                );
        } catch (ConnectionException $e) {
            throw new AIConnectionException(
                'Unable to reach the Gemini API.',
                previous: $e
            );
        }

        return $this->decode($response);
    }

    /**
     * Gemini expects `contents` to be a LIST of content objects.
     */
    private function payload(string $prompt, array $responseSchema): array
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        if ($responseSchema !== []) {
            // Structured output: the model is constrained to this shape by the
            // decoder rather than merely asked for it in the prompt.
            $payload['generationConfig'] = [
                'responseMimeType' => 'application/json',
                'responseSchema' => $this->toGeminiSchema($responseSchema),
            ];
        }

        return $payload;
    }

    private function decode(Response $response): array
    {
        if ($response->status() === 429) {
            throw new AIRateLimitException('Gemini rate limit exceeded.');
        }

        if ($response->failed()) {
            throw new AIResponseException(sprintf(
                'Gemini API request failed with status %d: %s',
                $response->status(),
                $response->body()
            ));
        }

        $decoded = $response->json();

        if (! is_array($decoded)) {
            throw new AIResponseException('Gemini returned a non-JSON body.');
        }

        return $decoded;
    }

    private function shouldRetry(\Throwable $e): bool
    {
        if ($e instanceof ConnectionException) {
            return true;
        }

        return $e instanceof RequestException
            && in_array($e->response->status(), self::RETRYABLE_STATUSES, true);
    }

    /**
     * Translate provider-neutral JSON Schema into Gemini's OpenAPI subset,
     * which names its types with upper-case enum members.
     */
    private function toGeminiSchema(array $schema): array
    {
        if (isset($schema['type']) && is_string($schema['type'])) {
            $schema['type'] = strtoupper($schema['type']);
        }

        foreach (['properties', 'items'] as $key) {
            if (! isset($schema[$key]) || ! is_array($schema[$key])) {
                continue;
            }

            $schema[$key] = $key === 'items'
                ? $this->toGeminiSchema($schema[$key])
                : array_map(fn (array $child) => $this->toGeminiSchema($child), $schema[$key]);
        }

        return $schema;
    }
}
