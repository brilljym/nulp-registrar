<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;

class RequestRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $requestType; // 'student' or 'onsite'
    public $remarks;

    /**
     * Create a new message instance.
     */
    public function __construct($request, string $requestType, string $remarks)
    {
        $this->request = $request;
        $this->requestType = $requestType;
        $this->remarks = $remarks;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->requestType === 'student'
            ? 'NU Lipa - Document Request Rejected'
            : 'NU Lipa - On-site Request Rejected';

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
            view: 'emails.request-rejected',
            with: [
                'request' => $this->request,
                'requestType' => $this->requestType,
                'remarks' => $this->remarks,
            ],
        );
    }
}