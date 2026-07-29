<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\AI\Execution\AIExecution;
use App\Services\AI\TicketAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzeTicketJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Ticket $ticket)
    {
    }

    public function handle(
        TicketAnalyzer $analyzer,
        AIExecution $execution
    ): void {

        $this->ticket->update([
            'status' => 'processing',
        ]);

        $analysis = $execution->run(
            callback: fn() => $analyzer->analyzeTicket($this->ticket),
            context: [
                'ticket_id' => $this->ticket->id,
            ]
        );

        $this->ticket->update([
            ...$analysis->toArray(),
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $e): void
    {
        $this->ticket->update([
            'status' => 'failed',
        ]);
    }
}