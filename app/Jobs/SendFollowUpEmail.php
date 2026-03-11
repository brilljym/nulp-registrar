<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\FollowUpEmail;
use App\Models\OnsiteRequest;
use App\Models\StudentRequest;

class SendFollowUpEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requestId;
    public $requestType;

    /**
     * Create a new job instance.
     */
    public function __construct($requestId, $requestType = 'onsite')
    {
        $this->requestId = $requestId;
        $this->requestType = $requestType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Follow-up email job started", [
            'request_id' => $this->requestId,
            'request_type' => $this->requestType
        ]);

        try {
            if ($this->requestType === 'onsite') {
                $request = OnsiteRequest::with(['requestItems.document', 'student.user'])->find($this->requestId);
            } else {
                $request = StudentRequest::with(['requestItems.document', 'student.user'])->find($this->requestId);
            }

            if (!$request) {
                Log::warning("Follow-up email job: Request not found", [
                    'request_id' => $this->requestId,
                    'request_type' => $this->requestType
                ]);
                return;
            }

            Log::info("Follow-up email job: Request found", [
                'request_id' => $this->requestId,
                'request_type' => $this->requestType,
                'status' => $request->status,
                'has_student' => $request->student ? true : false,
                'has_user' => $request->student && $request->student->user ? true : false
            ]);

            // Check if request is still completed and not already collected
            if ($request->status !== 'completed') {
                Log::info("Follow-up email job: Request status changed, skipping", [
                    'request_id' => $this->requestId,
                    'request_type' => $this->requestType,
                    'status' => $request->status
                ]);
                return;
            }

            // Get email address
            $email = null;
            if ($this->requestType === 'onsite') {
                if ($request->student && $request->student->user) {
                    $email = $request->student->user->personal_email ?? $request->student->user->school_email;
                }
            } else {
                if ($request->student && $request->student->user) {
                    $email = $request->student->user->personal_email ?? $request->student->user->school_email;
                }
            }

            if (!$email) {
                Log::warning("Follow-up email job: No email address found", [
                    'request_id' => $this->requestId,
                    'request_type' => $this->requestType
                ]);
                return;
            }

            Log::info("Follow-up email job: Sending email", [
                'request_id' => $this->requestId,
                'request_type' => $this->requestType,
                'email' => $email
            ]);

            // Send the follow-up email
            Mail::to($email)->send(new FollowUpEmail($request, $this->requestType));

            Log::info("Follow-up email sent successfully", [
                'request_id' => $this->requestId,
                'request_type' => $this->requestType,
                'email' => $email
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send follow-up email", [
                'request_id' => $this->requestId,
                'request_type' => $this->requestType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }
}