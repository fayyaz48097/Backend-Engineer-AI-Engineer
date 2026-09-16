<?php

namespace Tests\Feature\Email;

use App\Enums\EmailStatus;
use App\Jobs\SendTicketEmailJob;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketEmailApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function draftTicket(): Ticket
    {
        return Ticket::factory()->analyzed()->create([
            'email_subject' => 'Regarding your request',
            'email_body' => 'Dear Customer, we are on it. The Support Team',
            'email_status' => EmailStatus::Draft,
            'email_generated_at' => now(),
        ]);
    }

    public function test_the_endpoints_are_unavailable_while_the_feature_is_off(): void
    {
        config(['ai.email.enabled' => false]);

        $ticket = $this->draftTicket();

        $this->postJson("/api/tickets/{$ticket->id}/email/approve")
            ->assertStatus(503);
    }

    public function test_approving_queues_the_send(): void
    {
        config(['ai.email.enabled' => true]);
        Queue::fake();

        $ticket = $this->draftTicket();

        $this->postJson("/api/tickets/{$ticket->id}/email/approve")
            ->assertOk()
            ->assertJsonPath('data.email_status', 'approved');

        $ticket->refresh();

        $this->assertSame(EmailStatus::Approved, $ticket->email_status);
        $this->assertNotNull($ticket->email_approved_at);

        Queue::assertPushed(SendTicketEmailJob::class);
    }

    public function test_an_already_sent_email_cannot_be_approved_again(): void
    {
        config(['ai.email.enabled' => true]);
        Queue::fake();

        $ticket = $this->draftTicket();
        $ticket->update(['email_status' => EmailStatus::Sent, 'email_sent_at' => now()]);

        $this->postJson("/api/tickets/{$ticket->id}/email/approve")
            ->assertStatus(422);

        Queue::assertNotPushed(SendTicketEmailJob::class);
    }

    public function test_rejecting_stops_the_email(): void
    {
        config(['ai.email.enabled' => true]);
        Queue::fake();

        $ticket = $this->draftTicket();

        $this->postJson("/api/tickets/{$ticket->id}/email/reject")->assertOk();

        $this->assertSame(EmailStatus::Rejected, $ticket->refresh()->email_status);
        Queue::assertNotPushed(SendTicketEmailJob::class);
    }

    public function test_it_exposes_the_draft_for_review(): void
    {
        config(['ai.email.enabled' => true]);

        $ticket = $this->draftTicket();

        $this->getJson("/api/tickets/{$ticket->id}/email")
            ->assertOk()
            ->assertJsonPath('data.email_status', 'draft')
            ->assertJsonPath('data.email_subject', 'Regarding your request');
    }
}
