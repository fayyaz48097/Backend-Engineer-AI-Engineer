<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Deliberately NOT a ShouldQueue mailable: SendTicketEmailJob owns the
     * queueing, so retries, backoff and status bookkeeping live in one place
     * instead of being split across two queue mechanisms.
     */
    public function __construct(public Ticket $ticket) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->ticket->email_subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply',
            text: 'emails.ticket-reply-text',
            with: [
                'body' => $this->ticket->email_body,
                'ticketId' => $this->ticket->id,
            ],
        );
    }
}
