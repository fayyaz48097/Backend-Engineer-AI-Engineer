<?php

namespace Tests\Unit\AI;

use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Gemini\GeminiResponseParser;
use Tests\TestCase;

class GeminiResponseParserTest extends TestCase
{
    private function envelope(string $text): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]]];
    }

    private function parser(): GeminiResponseParser
    {
        return app(GeminiResponseParser::class);
    }

    public function test_it_parses_bare_json(): void
    {
        $data = $this->parser()->parse($this->envelope(json_encode([
            'category' => 'Billing',
            'priority' => 'Low',
            'summary' => 'A summary.',
            'suggested_reply' => 'A reply.',
        ])));

        $this->assertSame('Billing', $data->category);
        $this->assertSame('A reply.', $data->suggestedReply);
    }

    public function test_it_tolerates_markdown_fences_and_wrong_casing(): void
    {
        $data = $this->parser()->parse($this->envelope(<<<'TXT'
        ```json
        {"category":"technical","priority":"HIGH","summary":"S","suggested_reply":"R"}
        ```
        TXT));

        $this->assertSame('Technical', $data->category);
        $this->assertSame('High', $data->priority);
    }

    public function test_it_rejects_a_value_outside_the_allowed_list(): void
    {
        $this->expectException(InvalidAIResponseException::class);

        $this->parser()->parse($this->envelope(json_encode([
            'category' => 'Astrology',
            'priority' => 'Low',
            'summary' => 'S',
            'suggested_reply' => 'R',
        ])));
    }

    public function test_it_rejects_invalid_json(): void
    {
        $this->expectException(InvalidAIResponseException::class);

        $this->parser()->parse($this->envelope('not json at all'));
    }

    public function test_it_rejects_an_empty_candidate(): void
    {
        $this->expectException(InvalidAIResponseException::class);

        $this->parser()->parse(['candidates' => [['finishReason' => 'SAFETY']]]);
    }
}
