<?php

namespace App\Actions\Ticket;

use App\Jobs\AnalyzeTicketJob;
use App\Repositories\Ticket\TicketRepository;
use Illuminate\Support\Facades\DB;

class TicketAction
{
    public function __construct(protected TicketRepository $repository)
    {
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $created = $this->repository->create($data);
            if (!$created) {
                throw new \Exception(__('create_failed'), 422);
            }

            // afterCommit: without it, a worker on a non-database queue can
            // pick the job up before this transaction commits and fail with
            // "ticket not found".
            AnalyzeTicketJob::dispatch($created)->afterCommit();

            return $created;
        });
    }

    public function index()
    {
        return $this->repository->all();
    }
}