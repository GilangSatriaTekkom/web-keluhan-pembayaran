<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticketId;
    public $ticketCategory;
    public $ticketDescription;
    public $customerName;

    public function __construct($ticketId, $ticketCategory, $ticketDescription, $customerName)
    {
        $this->ticketId = $ticketId;
        $this->ticketCategory = $ticketCategory;
        $this->ticketDescription = $ticketDescription;
        $this->customerName = $customerName;
    }

    public function build()
    {
        return $this->subject("Tiket #{$this->ticketId} Telah Diselesaikan")
            ->view('emails.ticket-resolved')
            ->with([
                'ticketId' => $this->ticketId,
                'ticketCategory' => $this->ticketCategory,
                'ticketDescription' => $this->ticketDescription,
                'customerName' => $this->customerName,
            ]);
    }
}
