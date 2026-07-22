<?php

namespace App\Repositories\Ticket;

use App\Models\Ticket;

class TicketRepository
{
    protected Ticket $model;
    public function __construct(Ticket $model)
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