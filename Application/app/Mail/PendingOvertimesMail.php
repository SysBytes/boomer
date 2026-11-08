<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PendingOvertimesMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientName;
    public Collection $overtimes;
    public string $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct(string $recipientName, Collection $overtimes, string $recipientType)
    {
        $this->recipientName = $recipientName;
        $this->overtimes = $overtimes;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = $this->overtimes->count();
        return new Envelope(
            subject: "Horas Extras Pendientes por Aprobar ({$count})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending_overtimes',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
