<?php

namespace App\Http\Controllers\Ticket;

use App\Actions\Ticket\TicketAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;

class TicketController extends Controller
{
    public function __construct(protected TicketAction $action)
    {

    }

    public function index()
    {
        $records = $this->action->index();
        return response()->json([
            'message' => 'Tickets retrieved successfully',
            'data' => $records
        ], 200);
    }

    public function store(StoreTicketRequest $request)
    {
        $createdticket = $this->action->store($request->validated());

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $createdticket
        ], 201);
    }
}