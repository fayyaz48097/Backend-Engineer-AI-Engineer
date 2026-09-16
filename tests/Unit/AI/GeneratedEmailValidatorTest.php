<?php

namespace Tests\Unit\AI;

use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Validators\GeneratedEmailValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GeneratedEmailValidatorTest extends TestCase
{
    private function validate(array $overrides = []): array
    {
        return app(GeneratedEmailValidator::class)->validate([
            'subject' => 'Regarding your request',
            'body' => 'Dear Customer, thank you for reaching out to us today.',
            ...$overrides,
        ]);
    }

    public function test_it_accepts_a_well_formed_email(): void
    {
        $this->assertSame('Regarding your request', $this->validate()['subject']);
    }

    public function test_it_strips_newlines_from_the_subject(): void
    {
        $result = $this->validate(['subject' => "Hello\r\nBcc: attacker@evil.test"]);

        $this->assertStringNotContainsString("\n", $result['subject']);
        $this->assertStringNotContainsString("\r", $result['subject']);
    }

    #[DataProvider('placeholders')]
    public function test_it_rejects_unfilled_placeholders(string $body): void
    {
        $this->expectException(InvalidAIResponseException::class);

        $this->validate(['body' => $body]);
    }

    public static function placeholders(): array
    {
        return [
            'square brackets' => ['Dear [Customer Name], thanks for writing in to us.'],
            'handlebars' => ['Dear {{ name }}, thanks very much for writing in to us.'],
            'todo' => ['Dear Customer, TODO: explain the refund policy in detail.'],
        ];
    }

    public function test_it_rejects_an_over_long_body(): void
    {
        config(['ai.email.max_body_length' => 50]);

        $this->expectException(InvalidAIResponseException::class);

        $this->validate(['body' => str_repeat('a', 51)]);
    }

    public function test_it_rejects_an_over_long_subject(): void
    {
        $this->expectException(InvalidAIResponseException::class);

        $this->validate(['subject' => str_repeat('a', 121)]);
    }
}
