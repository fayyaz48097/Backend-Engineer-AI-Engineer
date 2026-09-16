<?php

namespace App\Jobs;

use App\Enums\EmailStatus;
use App\Models\Ticket;
use App\Services\AI\Email\EmailGenerator;
use App\Services\AI\Exceptions\AIRateLimitException;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Execution\AIExecution;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateTicketEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public int $timeout = 180;

    /**
     * @param  bool  $force  Regenerate a draft that already exists.
     */
    public function __construct(protected Ticket $ticket, protected bool $force = false) {}

    public function handle(
        EmailGenerator $generator,
        AIExecution $execution
    ): void {
        // Master switch. Checked inside the job as well as before dispatch so
        // that jobs already sitting on the queue go quiet the moment the flag
        // is turned off, without having to drain or purge anything.
        if (! config('ai.email.enabled', false)) {
            // Logged rather than returned silently: a long-running worker
            // holds the config it booted with, so the commonest reason to
            // land here is a worker that predates the flag being switched on
            // and needs `queue:restart`, not the flag actually being off.
            logger()->info('Ticket email not generated: email automation is disabled.', [
                'ticket_id' => $this->ticket->id,
            ]);

            return;
        }

        // Never write over an email that is already out the door.
        if ($this->ticket->email_status === EmailStatus::Sent) {
            return;
        }

        // Idempotency: a retry must not pay for a second generation.
        if (! $this->force && $this->ticket->email_generated_at !== null) {
            return;
        }

        try {
            $email = $execution->run(
                callback: fn () => $generator->generate($this->ticket),
                context: [
                    'ticket_id' => $this->ticket->id,
                    'stage' => 'email_generation',
                ],
            );
        } catch (AIRateLimitException $e) {
            $this->release(60);

            return;
        } catch (InvalidAIResponseException $e) {
            // The model produced something unusable (bad JSON, a placeholder,
            // an over-long body). Retrying the same prompt gets the same
            // answer, so stop and leave it for a human to look at.
            $this->fail($e);

            return;
        }

        $autoApprove = (bool) config('ai.email.auto_approve', false);

        $this->ticket->update([
            ...$email->toArray(),
            'email_status' => $autoApprove ? EmailStatus::Approved : EmailStatus::Draft,
            'email_generated_at' => now(),
            'email_approved_at' => $autoApprove ? now() : null,
            'email_error' => null,
        ]);

        if ($autoApprove) {
            SendTicketEmailJob::dispatch($this->ticket->fresh())->afterCommit();
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->ticket->update([
            'email_status' => EmailStatus::Failed,
            'email_error' => mb_substr($e->getMessage(), 0, 1000),
        ]);
    }
}
