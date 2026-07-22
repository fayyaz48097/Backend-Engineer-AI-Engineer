<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\AI\TicketAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzeTicketJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Ticket $ticket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TicketAnalyzer $analyzer): void
    {
        logger()->info("AI Analysis Started for Ticket #{$this->ticket->id}");

        // Update status to processing
        $this->ticket->update([
            'status' => 'processing',
        ]);

        $analysis = $analyzer->analyzeTicket(
            $this->ticket
        );

        $this->ticket->update([
            ...$analysis->toArray(),
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);
        logger()->info("AI Analysis Completed for Ticket #{$this->ticket->id}");
    }
}