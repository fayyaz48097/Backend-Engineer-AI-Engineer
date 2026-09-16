<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\AI\Exceptions\AIRateLimitException;
use App\Services\AI\Exceptions\InvalidAIResponseException;
use App\Services\AI\Execution\AIExecution;
use App\Services\AI\TicketAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzeTicketJob implements ShouldQueue
{
    use Queueable;

    /**
     * A transient 503 from the provider should not permanently fail a ticket.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before each retry. Longer than the HTTP-level retry
     * inside the client, which handles the sub-second blips.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Comfortably above the AI timeout plus its own internal retries.
     */
    public int $timeout = 180;

    public function __construct(protected Ticket $ticket)
    {
    }

    public function handle(
        TicketAnalyzer $analyzer,
        AIExecution $execution
    ): void {
        // Idempotency: a retry (or a duplicate dispatch) must not pay for a
        // second analysis or overwrite a result that already landed.
        if ($this->ticket->analyzed_at !== null) {
            return;
        }

        $this->ticket->update(['status' => 'processing']);

        try {
            $analysis = $execution->run(
                callback: fn() => $analyzer->analyzeTicket($this->ticket),
                context: ['ticket_id' => $this->ticket->id],
            );
        } catch (AIRateLimitException $e) {
            // Not our fault and not fixable by trying immediately: put the job
            // back on the queue and let the rate-limit window pass.
            $this->release(60);

            return;
        } catch (InvalidAIResponseException $e) {
            // Deterministic: the same prompt will produce the same unusable
            // answer, so burn the attempts budget rather than the API quota.
            $this->fail($e);

            return;
        }

        $this->ticket->update([
            ...$analysis->toArray(),
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);
    }

    /**
     * Handle a job failure (after the final attempt).
     */
    public function failed(\Throwable $e): void
    {
        $this->ticket->update(['status' => 'failed']);
    }
}