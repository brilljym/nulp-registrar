<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FollowUpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $requestType;

    /**
     * Create a new message instance.
     */
    public function __construct($request, $requestType = 'onsite')
    {
        $this->request = $request;
        $this->requestType = $requestType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $refCode = $this->requestType === 'onsite' ? $this->request->ref_code : $this->request->reference_no;
        return new Envelope(
            subject: 'NU Lipa - Reminder: Your Document Request is Ready for Pickup',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.follow-up-reminder',
            with: [
                'request' => $this->request,
                'requestType' => $this->requestType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}