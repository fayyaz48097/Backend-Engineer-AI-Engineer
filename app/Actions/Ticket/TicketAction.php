<?php

namespace App\Actions\Ticket;

use App\Repositories\Ticket\TicketRepository;
use App\Jobs\AnalyzeTicketJob;
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
                throw new \Exception(__("create_failed"), 422);
            }
            AnalyzeTicketJob::dispatch($created);
            return $created;
        });
    }

    public function index()
    {
        return $this->repository->all();
    }
}