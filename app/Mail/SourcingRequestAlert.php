<?php

namespace App\Mail;

use App\Models\SourcingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SourcingRequestAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SourcingRequest $sourcingRequest
    ) {}

    public function envelope(): Envelope
    {
        $part = strtoupper($this->sourcingRequest->part_number);
        $company = $this->sourcingRequest->company_name
            ? " from {$this->sourcingRequest->company_name}"
            : '';

        return new Envelope(
            subject: "🔔 New Sourcing Request [{$this->sourcingRequest->reference_number}]: {$part}{$company}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.sourcing-request-alert',
            with: [
                'request'      => $this->sourcingRequest,
                'hasSuggestions' => ! empty($this->sourcingRequest->suggested_alternatives),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
