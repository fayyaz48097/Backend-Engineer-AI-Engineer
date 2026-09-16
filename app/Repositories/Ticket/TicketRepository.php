<?php

namespace App\Repositories\Ticket;

use App\Models\Ticket;

class TicketRepository
{
    public function __construct(protected Ticket $model)
    {
        $this->model = $model;
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function all()
    {
        return $this->model->get();
    }
}