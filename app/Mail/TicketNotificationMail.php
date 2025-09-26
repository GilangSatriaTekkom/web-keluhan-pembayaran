<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;


    public $subjectText;
    public $messageText;
    public $ticketId;
    public $ticketCategory;
    public $ticketDescription;
    public $teknisiName;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectText, $messageText, $ticketId, $ticketCategory, $ticketDescription, $teknisiName)
    {
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
        $this->ticketId = $ticketId;
        $this->ticketCategory = $ticketCategory;
        $this->ticketDescription = $ticketDescription;
        $this->teknisiName = $teknisiName;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.ticket-notification')
                    ->with([
                        'messageText' => $this->messageText,
                        'ticketId' => $this->ticketId,
                        'ticketCategory' => $this->ticketCategory,
                        'ticketDescription' => $this->ticketDescription,
                        'teknisiName' => $this->teknisiName,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Ticket Notification Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

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
