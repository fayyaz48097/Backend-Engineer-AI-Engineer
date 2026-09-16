<?php

namespace Tests\Feature\Ticket;

use App\Jobs\AnalyzeTicketJob;
use App\Models\Ticket;
use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Exceptions\AIConnectionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyzeTicketJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_writes_the_analysis_back_to_the_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        AnalyzeTicketJob::dispatchSync($ticket);

        $ticket->refresh();

        $this->assertSame('completed', $ticket->status);
        $this->assertSame('Technical', $ticket->category);
        $this->assertSame('High', $ticket->priority);
        $this->assertSame('This is a fake AI response.', $ticket->summary);
        $this->assertNotNull($ticket->analyzed_at);
    }

    public function test_it_skips_a_ticket_that_was_already_analyzed(): void
    {
        $ticket = Ticket::factory()->analyzed()->create();

        // Any call to the AI would fail this test.
        $this->mock(AIClient::class)->shouldNotReceive('generate');

        AnalyzeTicketJob::dispatchSync($ticket);

        $ticket->refresh();

        $this->assertSame('Billing', $ticket->category);
        $this->assertSame('Already summarised.', $ticket->summary);
    }

    public function test_it_marks_the_ticket_failed_when_the_provider_is_unreachable(): void
    {
        $ticket = Ticket::factory()->create();

        $this->mock(AIClient::class)
            ->shouldReceive('generate')
            ->andThrow(new AIConnectionException('boom'));

        try {
            AnalyzeTicketJob::dispatchSync($ticket);
        } catch (AIConnectionException) {
            // dispatchSync rethrows after failed() has run.
        }

        $this->assertSame('failed', $ticket->refresh()->status);
    }
}
