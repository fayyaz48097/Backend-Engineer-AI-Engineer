<?php

namespace Tests\Feature\Email;

use App\Enums\EmailStatus;
use App\Jobs\GenerateTicketEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Ticket;
use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateTicketEmailJobTest extends TestCase
{
    use RefreshDatabase;

    private function analyzedTicket(): Ticket
    {
        return Ticket::factory()->analyzed()->create();
    }

    public function test_it_does_nothing_while_the_feature_flag_is_off(): void
    {
        config(['ai.email.enabled' => false]);

        $ticket = $this->analyzedTicket();

        $this->mock(AIClient::class)->shouldNotReceive('generate');

        GenerateTicketEmailJob::dispatchSync($ticket);

        $this->assertSame(EmailStatus::Pending, $ticket->refresh()->email_status);
        $this->assertNull($ticket->email_body);
    }

    public function test_it_stores_a_draft_awaiting_approval(): void
    {
        config(['ai.email.enabled' => true, 'ai.email.auto_approve' => false]);

        $ticket = $this->analyzedTicket();

        Queue::fake([SendTicketEmailJob::class]);

        GenerateTicketEmailJob::dispatchSync($ticket);

        $ticket->refresh();

        $this->assertSame(EmailStatus::Draft, $ticket->email_status);
        $this->assertSame('Regarding your support request', $ticket->email_subject);
        $this->assertStringContainsString('Dear Customer', $ticket->email_body);
        $this->assertNotNull($ticket->email_generated_at);
        $this->assertNull($ticket->email_approved_at);

        // A draft must never reach the sender on its own.
        Queue::assertNotPushed(SendTicketEmailJob::class);
    }

    public function test_auto_approve_queues_the_send(): void
    {
        config(['ai.email.enabled' => true, 'ai.email.auto_approve' => true]);

        Queue::fake([SendTicketEmailJob::class]);

        GenerateTicketEmailJob::dispatchSync($this->analyzedTicket());

        Queue::assertPushed(SendTicketEmailJob::class);
    }

    public function test_it_does_not_regenerate_an_existing_draft(): void
    {
        config(['ai.email.enabled' => true]);

        $ticket = $this->analyzedTicket();
        $ticket->update([
            'email_subject' => 'Existing',
            'email_body' => 'Existing body',
            'email_status' => EmailStatus::Draft,
            'email_generated_at' => now(),
        ]);

        $this->mock(AIClient::class)->shouldNotReceive('generate');

        GenerateTicketEmailJob::dispatchSync($ticket);

        $this->assertSame('Existing', $ticket->refresh()->email_subject);
    }

    public function test_it_never_overwrites_an_email_that_was_already_sent(): void
    {
        config(['ai.email.enabled' => true]);

        $ticket = $this->analyzedTicket();
        $ticket->update([
            'email_subject' => 'Delivered',
            'email_status' => EmailStatus::Sent,
            'email_sent_at' => now(),
        ]);

        $this->mock(AIClient::class)->shouldNotReceive('generate');

        // force must not override the sent guard.
        GenerateTicketEmailJob::dispatchSync($ticket, force: true);

        $this->assertSame('Delivered', $ticket->refresh()->email_subject);
    }

    public function test_an_unusable_response_marks_the_email_failed(): void
    {
        config(['ai.email.enabled' => true]);

        $ticket = $this->analyzedTicket();

        $this->mock(AIClient::class)
            ->shouldReceive('generate')
            ->andThrow(new InvalidAIResponseException('placeholder found'));

        try {
            GenerateTicketEmailJob::dispatchSync($ticket);
        } catch (InvalidAIResponseException) {
            // dispatchSync rethrows after failed() has run.
        }

        $ticket->refresh();

        $this->assertSame(EmailStatus::Failed, $ticket->email_status);
        $this->assertStringContainsString('placeholder found', $ticket->email_error);
    }
}
