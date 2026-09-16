<?php

namespace App\Actions\Ticket;

use App\Enums\EmailStatus;
use App\Jobs\GenerateTicketEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TicketEmailAction
{
    /**
     * Queue a draft for a ticket that has been analyzed but has no email yet.
     */
    public function generate(Ticket $ticket, bool $force = false): Ticket
    {
        $this->assertEmailAutomationEnabled();

        if ($ticket->analyzed_at === null) {
            throw new RuntimeException('Ticket has not been analyzed yet.', 422);
        }

        GenerateTicketEmailJob::dispatch($ticket, $force);

        return $ticket;
    }

    /**
     * The human decision point. Approving is what moves an email from
     * "the AI wrote this" to "we are willing to send this".
     */
    public function approve(Ticket $ticket): Ticket
    {
        $this->assertEmailAutomationEnabled();

        if (! $ticket->email_status?->canBeApproved()) {
            throw new RuntimeException(sprintf(
                'Email cannot be approved from status [%s].',
                $ticket->email_status?->value ?? 'unknown'
            ), 422);
        }

        return DB::transaction(function () use ($ticket) {
            $ticket->update([
                'email_status' => EmailStatus::Approved,
                'email_approved_at' => now(),
                'email_error' => null,
            ]);

            SendTicketEmailJob::dispatch($ticket->fresh())->afterCommit();

            return $ticket->fresh();
        });
    }

    public function reject(Ticket $ticket): Ticket
    {
        $this->assertEmailAutomationEnabled();

        if ($ticket->email_status?->isTerminal()) {
            throw new RuntimeException(sprintf(
                'Email cannot be rejected from status [%s].',
                $ticket->email_status->value
            ), 422);
        }

        $ticket->update(['email_status' => EmailStatus::Rejected]);

        return $ticket->fresh();
    }

    private function assertEmailAutomationEnabled(): void
    {
        if (! config('ai.email.enabled', false)) {
            throw new RuntimeException('Email automation is disabled.', 503);
        }
    }
}
