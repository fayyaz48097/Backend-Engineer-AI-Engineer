<?php

namespace Tests\Unit\AI;

use App\Services\AI\Exceptions\AIRateLimitException;
use App\Services\AI\Exceptions\AIResponseException;
use App\Services\AI\Gemini\GeminiClient;
use App\Services\AI\Prompts\TicketPromptBuilder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiClientTest extends TestCase
{
    private const ENDPOINT = 'generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai.provider' => 'gemini',
            'ai.retry.times' => 3,
            'ai.retry.delay' => 0,
            'services.gemini.api_key' => 'test-key',
        ]);
    }

    public function test_it_sends_the_documented_payload_shape_and_authenticates_by_header(): void
    {
        Http::fake([self::ENDPOINT => Http::response(['candidates' => []])]);

        app(GeminiClient::class)->generate(
            'Hello',
            app(TicketPromptBuilder::class)->responseSchema()
        );

        Http::assertSent(function (Request $request) {
            $body = $request->data();

            return $request->hasHeader('x-goog-api-key', 'test-key')
                && ! str_contains($request->url(), 'test-key')
                // contents is a LIST of content objects
                && array_is_list($body['contents'])
                && $body['contents'][0]['parts'][0]['text'] === 'Hello'
                && $body['generationConfig']['responseMimeType'] === 'application/json'
                // schema types are upper-cased for Gemini's OpenAPI subset
                && $body['generationConfig']['responseSchema']['type'] === 'OBJECT'
                && $body['generationConfig']['responseSchema']['properties']['category']['type'] === 'STRING';
        });
    }

    public function test_the_kill_switch_disables_structured_output(): void
    {
        config(['ai.structured_output' => false]);

        Http::fake([self::ENDPOINT => Http::response(['candidates' => []])]);

        app(GeminiClient::class)->generate(
            'Hello',
            app(TicketPromptBuilder::class)->responseSchema()
        );

        Http::assertSent(fn (Request $request) => ! isset($request->data()['generationConfig']));
    }

    public function test_it_retries_retryable_status_codes(): void
    {
        Http::fake([self::ENDPOINT => Http::sequence()
            ->push(['error' => 'unavailable'], 503)
            ->push(['candidates' => [['ok' => true]]], 200),
        ]);

        $result = app(GeminiClient::class)->generate('Hello');

        $this->assertArrayHasKey('candidates', $result);
        Http::assertSentCount(2);
    }

    public function test_it_does_not_retry_a_bad_request(): void
    {
        Http::fake([self::ENDPOINT => Http::response(['error' => 'bad'], 400)]);

        try {
            app(GeminiClient::class)->generate('Hello');
            $this->fail('Expected AIResponseException.');
        } catch (AIResponseException) {
            // expected
        }

        Http::assertSentCount(1);
    }

    public function test_it_raises_a_typed_rate_limit_error(): void
    {
        Http::fake([self::ENDPOINT => Http::response(['error' => 'slow down'], 429)]);

        $this->expectException(AIRateLimitException::class);

        app(GeminiClient::class)->generate('Hello');
    }
}
