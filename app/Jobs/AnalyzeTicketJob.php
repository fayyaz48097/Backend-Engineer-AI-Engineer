<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\AI\Exceptions\AIException;
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
        try {

            logger()->info("AI Analysis Started for Ticket #{$this->ticket->id}");


            $this->ticket->update([
                'status' => 'processing',
            ]);

            $analysis = $analyzer->analyzeTicket($this->ticket);

        } catch (AIException $e) {

            $this->ticket->update([
                'status' => 'failed',
            ]);

            logger()->error($e->getMessage(), [
                'ticket_id' => $this->ticket->id,
                'exception' => get_class($e),
            ]);

            throw $e;
        }
        $this->ticket->update([
            ...$analysis->toArray(),
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);
        logger()->info("AI Analysis Completed for Ticket #{$this->ticket->id}");
    }
}