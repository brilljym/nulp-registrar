<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\OnsiteRequest;
use App\Models\StudentRequest;

class ExpectedReleaseDateUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $requestType; // 'student' or 'onsite'

    /**
     * Create a new message instance.
     */
    public function __construct($request, string $requestType = 'onsite')
    {
        $this->request = $request;
        $this->requestType = $requestType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->requestType === 'student'
            ? 'NU Lipa - Updated Expected Release Date for Your Document Request'
            : 'NU Lipa - Updated Expected Release Date for Your On-site Request';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.expected-release-date-updated',
            with: [
                'request' => $this->request,
                'requestType' => $this->requestType,
            ],
        );
    }
}