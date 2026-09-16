<?php

namespace Tests\Feature\Email;

use App\Enums\EmailStatus;
use App\Jobs\SendTicketEmailJob;
use App\Mail\TicketReplyMail;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendTicketEmailJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config([
            'ai.email.enabled' => true,
            'ai.email.sending_enabled' => true,
        ]);
    }

    private function approvedTicket(array $overrides = []): Ticket
    {
        return Ticket::factory()->analyzed()->create([
            'email_subject' => 'Regarding your request',
            'email_body' => 'Dear Customer, we are on it. The Support Team',
            'email_status' => EmailStatus::Approved,
            'email_approved_at' => now(),
            ...$overrides,
        ]);
    }

    public function test_it_sends_an_approved_email(): void
    {
        $ticket = $this->approvedTicket();

        SendTicketEmailJob::dispatchSync($ticket);

        Mail::assertSent(TicketReplyMail::class, fn ($mail) => $mail->hasTo($ticket->email));

        $ticket->refresh();

        $this->assertSame(EmailStatus::Sent, $ticket->email_status);
        $this->assertNotNull($ticket->email_sent_at);
    }

    public function test_the_master_switch_blocks_delivery_without_failing(): void
    {
        config(['ai.email.sending_enabled' => false]);

        $ticket = $this->approvedTicket();

        SendTicketEmailJob::dispatchSync($ticket);

        Mail::assertNothingSent();

        // Still approved, not failed: it can go out once sending is enabled.
        $this->assertSame(EmailStatus::Approved, $ticket->refresh()->email_status);
    }

    public function test_it_refuses_to_send_an_unapproved_draft(): void
    {
        $ticket = $this->approvedTicket(['email_status' => EmailStatus::Draft]);

        SendTicketEmailJob::dispatchSync($ticket);

        Mail::assertNothingSent();
    }

    public function test_it_never_sends_the_same_email_twice(): void
    {
        $ticket = $this->approvedTicket([
            'email_status' => EmailStatus::Sent,
            'email_sent_at' => now(),
        ]);

        SendTicketEmailJob::dispatchSync($ticket);

        Mail::assertNothingSent();
    }

    public function test_it_fails_on_an_undeliverable_address(): void
    {
        $ticket = $this->approvedTicket(['email' => 'not-an-email']);

        try {
            SendTicketEmailJob::dispatchSync($ticket);
        } catch (\Throwable) {
            // dispatchSync rethrows after failed() has run.
        }

        Mail::assertNothingSent();
        $this->assertSame(EmailStatus::Failed, $ticket->refresh()->email_status);
    }

    public function test_it_fails_when_the_body_is_missing(): void
    {
        $ticket = $this->approvedTicket(['email_body' => null]);

        try {
            SendTicketEmailJob::dispatchSync($ticket);
        } catch (\Throwable) {
            // dispatchSync rethrows after failed() has run.
        }

        Mail::assertNothingSent();
        $this->assertSame(EmailStatus::Failed, $ticket->refresh()->email_status);
    }
}
