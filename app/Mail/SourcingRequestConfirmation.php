<?php

namespace App\Mail;

use App\Models\SourcingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SourcingRequestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SourcingRequest $sourcingRequest
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Sourcing request received: {$this->sourcingRequest->reference_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer.sourcing-request-confirmation',
            with: [
                'request' => $this->sourcingRequest,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}