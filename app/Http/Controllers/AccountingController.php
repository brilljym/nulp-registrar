<?php

namespace App\Http\Controllers;

use App\Models\OnsiteRequest;
use App\Models\StudentRequest;
use App\Services\RealTimeNotificationService;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentApprovedMail;

class AccountingController extends Controller
{
    protected $notificationService;
    protected $queueService;

    public function __construct(RealTimeNotificationService $notificationService, QueueService $queueService)
    {
        $this->notificationService = $notificationService;
        $this->queueService = $queueService;
    }

    public function index()
    {
        $pendingOnsiteRequests = OnsiteRequest::where("current_step", "payment")
            ->where("payment_receipt_path", "!=", null)
            ->where("payment_approved", false)
            ->where("status", "registrar_approved")
            ->with(["requestItems.document", "student"])
            ->orderBy("created_at", "asc")
            ->get();

        $pendingStudentRequests = StudentRequest::where("payment_receipt_path", "!=", null)
            ->where("payment_approved", false)
            ->where("status", "registrar_approved")
            ->with(["requestItems.document", "student.user"])
            ->orderBy("created_at", "asc")
            ->get();

        $pendingRequests = $pendingOnsiteRequests->concat($pendingStudentRequests)->sortBy("created_at");

        return view("accounting.dashboard", compact("pendingRequests"));
    }

    public function approveOnsite(Request $request, OnsiteRequest $onsiteRequest)
    {
        return $this->approveOnsiteRequest($onsiteRequest);
    }

    public function approveStudent(Request $request, StudentRequest $studentRequest)
    {
        return $this->approveStudentRequest($studentRequest);
    }

    public function rejectOnsite(Request $request, OnsiteRequest $onsiteRequest)
    {
        return $this->rejectOnsiteRequest($request, $onsiteRequest);
    }

    public function rejectStudent(Request $request, StudentRequest $studentRequest)
    {
        return $this->rejectStudentRequest($request, $studentRequest);
    }

    public function viewOnsiteReceipt(OnsiteRequest $onsiteRequest)
    {
        if (!$onsiteRequest->payment_receipt_path) {
            return redirect()->back()->with("error", "No payment receipt found.");
        }

        return response()->file(storage_path("app/public/" . $onsiteRequest->payment_receipt_path));
    }

    public function viewStudentReceipt(StudentRequest $studentRequest)
    {
        if (!$studentRequest->payment_receipt_path) {
            return redirect()->back()->with("error", "No payment receipt found.");
        }

        return response()->file(storage_path("app/public/" . $studentRequest->payment_receipt_path));
    }

    private function approveOnsiteRequest(OnsiteRequest $onsiteRequest)
    {
        // Check if user has accounting role
        if (Auth::user()->role->name !== "accounting") {
            return redirect()->back()->with("error", "Unauthorized access.");
        }

        if ($onsiteRequest->payment_approved) {
            return redirect()->back()->with("info", "Request already approved.");
        }

        if (!$onsiteRequest->payment_receipt_path) {
            return redirect()->back()->with("error", "No payment receipt uploaded.");
        }

        // Ensure windows are freed for any completed requests before checking availability
        $this->queueService->freeWindowsForCompletedRequests();

        // Find available window
        $availableWindow = \App\Models\Window::where("is_occupied", false)->first();

        if (!$availableWindow) {
            return redirect()->back()->with("error", "No available window at the moment. Payment approved but window assignment pending.");
        }

        // Approve the payment and assign window
        $onsiteRequest->update([
            "payment_approved" => true,
            "approved_by_accounting_id" => Auth::id(),
            "payment_approved_at" => now(),
            "current_step" => "window", // Move to window assignment
            "status" => "processing",
            "window_id" => $availableWindow->id
        ]);

        // Mark window as occupied
        $availableWindow->update(["is_occupied" => true]);

        // Try to assign registrar and move to processing immediately
        $assignedRegistrar = $this->queueService->assignRegistrarToRequest($onsiteRequest);

        if ($assignedRegistrar) {
            // Move directly to processing if registrar is available
            $onsiteRequest->update([
                "current_step" => "processing",
                "status" => "processing"
            ]);

            $message = "Request {$onsiteRequest->ref_code} assigned to Window {$availableWindow->name} and Registrar {$assignedRegistrar->user->first_name} {$assignedRegistrar->user->last_name}.";
        } else {
            $message = "Request {$onsiteRequest->ref_code} assigned to Window {$availableWindow->name} and waiting for registrar assignment.";
        }

        // Send notification to user
        $this->notificationService->sendNotification(
            "Payment approved for request {$onsiteRequest->ref_code}",
            "payment-approved",
            [
                "request_id" => $onsiteRequest->id,
                "ref_code" => $onsiteRequest->ref_code,
                "student_name" => $onsiteRequest->full_name,
                "status_update" => true
            ],
            ["request-{$onsiteRequest->ref_code}"]
        );

        // Send notification to registrars about payment approval
        $this->notificationService->sendNotification(
            "Payment approved for onsite request {$onsiteRequest->ref_code} - ready for processing",
            "payment-approved",
            [
                "request_id" => $onsiteRequest->id,
                "ref_code" => $onsiteRequest->ref_code,
                "student_name" => $onsiteRequest->full_name,
                "current_step" => $onsiteRequest->current_step,
                "window_id" => $onsiteRequest->window_id,
                "assigned_registrar_id" => $onsiteRequest->assigned_registrar_id,
                "request_type" => "onsite",
                "status_update" => true
            ],
            ["registrar-notifications", "onsite-request-updates"]
        );

        // Send email notification to student if email is available
        if ($onsiteRequest->student && $onsiteRequest->student->user) {
            $email = $onsiteRequest->student->user->personal_email ?? $onsiteRequest->student->user->school_email;
            if ($email) {
                try {
                    Mail::to($email)->send(new PaymentApprovedMail($onsiteRequest, 'onsite'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    \Illuminate\Support\Facades\Log::error('Failed to send payment approved email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with("success", $message);
    }

    private function approveStudentRequest(StudentRequest $studentRequest)
    {
        // Check if user has accounting role
        if (Auth::user()->role->name !== "accounting") {
            return redirect()->back()->with("error", "Unauthorized access.");
        }

        if ($studentRequest->payment_approved) {
            return redirect()->back()->with("info", "Request already approved.");
        }

        if (!$studentRequest->payment_receipt_path) {
            return redirect()->back()->with("error", "No payment receipt uploaded.");
        }

        // Approve the payment
        $studentRequest->update([
            "payment_approved" => true,
            "payment_confirmed" => true,
            "approved_by_accounting_id" => Auth::id(),
            "payment_approved_at" => now(),
            "status" => "processing"
        ]);

        // Send notification to user
        $this->notificationService->sendNotification(
            "Payment approved for document request {$studentRequest->reference_no}",
            "payment-approved",
            [
                "request_id" => $studentRequest->id,
                "ref_code" => $studentRequest->reference_no,
                "student_name" => $studentRequest->student->user->first_name . " " . $studentRequest->student->user->last_name,
                "status_update" => true
            ],
            ["request-{$studentRequest->reference_no}"]
        );

        // Send notification to registrars about payment approval
        $this->notificationService->sendNotification(
            "Payment approved for document request {$studentRequest->reference_no} - ready for processing",
            "payment-approved",
            [
                "request_id" => $studentRequest->id,
                "ref_code" => $studentRequest->reference_no,
                "student_name" => $studentRequest->student->user->first_name . " " . $studentRequest->student->user->last_name,
                "request_type" => "student",
                "status_update" => true
            ],
            ["registrar-notifications", "student-request-updates"]
        );

        // Send email notification to student
        $email = $studentRequest->student->user->personal_email ?? $studentRequest->student->user->school_email;
        if ($email) {
            try {
                Mail::to($email)->send(new PaymentApprovedMail($studentRequest, 'student'));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Illuminate\Support\Facades\Log::error('Failed to send payment approved email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with("success", "Document request {$studentRequest->reference_no} payment approved and moved to processing.");
    }

    private function rejectOnsiteRequest(Request $request, OnsiteRequest $onsiteRequest)
    {
        // Check if user has accounting role
        if (Auth::user()->role->name !== "accounting") {
            return redirect()->back()->with("error", "Unauthorized access.");
        }

        $rejectionReason = $request->rejection_reason ?? "Payment rejected by accounting";

        // Delete the payment receipt file
        if ($onsiteRequest->payment_receipt_path) {
            Storage::disk("public")->delete($onsiteRequest->payment_receipt_path);
        }

        // Reset payment status
        $onsiteRequest->update([
            "payment_receipt_path" => null,
            "payment_approved" => false,
            "approved_by_accounting_id" => null,
            "payment_approved_at" => null,
        ]);

        // Send notification to user
        $this->notificationService->sendNotification(
            "Payment rejected for request {$onsiteRequest->ref_code}: {$rejectionReason}",
            "payment-rejected",
            [
                "request_id" => $onsiteRequest->id,
                "ref_code" => $onsiteRequest->ref_code,
                "student_name" => $onsiteRequest->full_name,
                "rejection_reason" => $rejectionReason,
            ]
        );

        return redirect()->back()->with("success", "Payment rejected. User can upload a new receipt.");
    }

    private function rejectStudentRequest(Request $request, StudentRequest $studentRequest)
    {
        // Check if user has accounting role
        if (Auth::user()->role->name !== "accounting") {
            return redirect()->back()->with("error", "Unauthorized access.");
        }

        $rejectionReason = $request->rejection_reason ?? "Payment rejected by accounting";

        // Delete the payment receipt file
        if ($studentRequest->payment_receipt_path) {
            Storage::disk("public")->delete($studentRequest->payment_receipt_path);
        }

        // Reset payment status
        $studentRequest->update([
            "payment_receipt_path" => null,
            "payment_approved" => false,
            "payment_confirmed" => false,
            "approved_by_accounting_id" => null,
            "payment_approved_at" => null,
        ]);

        // Send notification to user
        $this->notificationService->sendNotification(
            "Payment rejected for document request {$studentRequest->reference_no}: {$rejectionReason}",
            "payment-rejected",
            [
                "request_id" => $studentRequest->id,
                "ref_code" => $studentRequest->reference_no,
                "student_name" => $studentRequest->student->user->first_name . " " . $studentRequest->student->user->last_name,
                "rejection_reason" => $rejectionReason,
            ]
        );

        return redirect()->back()->with("success", "Payment rejected. User can upload a new receipt.");
    }
}
