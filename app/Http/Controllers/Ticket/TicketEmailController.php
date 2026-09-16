<?php

namespace App\Http\Controllers\Ticket;

use App\Actions\Ticket\TicketEmailAction;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * NOTE: these routes are unauthenticated, exactly like the rest of this API.
 * Approving an email causes mail to be sent to a real customer, so this
 * controller MUST sit behind authentication before AI_EMAIL_SENDING_ENABLED
 * is turned on in production. See routes/api.php.
 */
class TicketEmailController extends Controller
{
    public function __construct(protected TicketEmailAction $action) {}

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json([
            'message' => 'Ticket email retrieved successfully',
            'data' => [
                'ticket_id' => $ticket->id,
                'email_status' => $ticket->email_status?->value,
                'email_subject' => $ticket->email_subject,
                'email_body' => $ticket->email_body,
                'email_generated_at' => $ticket->email_generated_at,
                'email_approved_at' => $ticket->email_approved_at,
                'email_sent_at' => $ticket->email_sent_at,
                'email_error' => $ticket->email_error,
            ],
        ], 200);
    }

    public function generate(Ticket $ticket): JsonResponse
    {
        return $this->guard(
            fn () => $this->action->generate($ticket, request()->boolean('force')),
            'Email generation queued'
        );
    }

    public function approve(Ticket $ticket): JsonResponse
    {
        return $this->guard(
            fn () => $this->action->approve($ticket),
            'Email approved and queued for sending'
        );
    }

    public function reject(Ticket $ticket): JsonResponse
    {
        return $this->guard(
            fn () => $this->action->reject($ticket),
            'Email rejected'
        );
    }

    /**
     * Turn the action's RuntimeExceptions into the status code they carry,
     * so a disabled feature reads as 503 and a bad state transition as 422
     * instead of both surfacing as a 500.
     */
    private function guard(callable $callback, string $message): JsonResponse
    {
        try {
            $ticket = $callback();
        } catch (RuntimeException $e) {
            $status = $e->getCode();

            return response()->json([
                'message' => $e->getMessage(),
            ], in_array($status, [422, 503], true) ? $status : 500);
        }

        return response()->json([
            'message' => $message,
            'data' => [
                'ticket_id' => $ticket->id,
                'email_status' => $ticket->email_status?->value,
            ],
        ], 200);
    }
}
