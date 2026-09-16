<?php

namespace App\Jobs;

use App\Enums\EmailStatus;
use App\Mail\TicketReplyMail;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTicketEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public int $timeout = 60;

    public function __construct(protected Ticket $ticket) {}

    public function handle(): void
    {
        // Every guard below is re-checked here rather than trusted from the
        // dispatch site, because a job can sit on the queue for minutes while
        // a flag flips, an email gets rejected, or someone sends it by hand.

        if (! config('ai.email.sending_enabled', false)) {
            logger()->info('Ticket email not sent: sending is disabled.', [
                'ticket_id' => $this->ticket->id,
            ]);

            // Left in Approved, not Failed: nothing went wrong, delivery is
            // simply switched off. It can be dispatched again once enabled.
            return;
        }

        // Idempotency. The strongest guard in this class: whatever else
        // happens, a customer must never receive the same email twice.
        if ($this->ticket->email_sent_at !== null) {
            return;
        }

        if (! $this->ticket->email_status?->canBeSent()) {
            logger()->warning('Ticket email not sent: status does not permit it.', [
                'ticket_id' => $this->ticket->id,
                'email_status' => $this->ticket->email_status?->value,
            ]);

            return;
        }

        if (! $this->hasSendableContent()) {
            $this->fail(new \RuntimeException(
                'Ticket email is missing a subject or body.'
            ));

            return;
        }

        if (! filter_var($this->ticket->email, FILTER_VALIDATE_EMAIL)) {
            $this->fail(new \RuntimeException(
                'Ticket has no deliverable email address.'
            ));

            return;
        }

        $this->ticket->update(['email_status' => EmailStatus::Queued]);

        Mail::to($this->ticket->email)->send(new TicketReplyMail($this->ticket));

        $this->ticket->update([
            'email_status' => EmailStatus::Sent,
            'email_sent_at' => now(),
            'email_error' => null,
        ]);

        logger()->info('Ticket email sent.', [
            'ticket_id' => $this->ticket->id,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->ticket->update([
            'email_status' => EmailStatus::Failed,
            'email_error' => mb_substr($e->getMessage(), 0, 1000),
        ]);
    }

    private function hasSendableContent(): bool
    {
        return filled($this->ticket->email_subject)
            && filled($this->ticket->email_body);
    }
}
