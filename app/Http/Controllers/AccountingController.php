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

        return view("accounting.dashboard", compact("pendingOnsiteRequests", "pendingStudentRequests"));
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

    public function history()
    {
        // Get approved onsite requests with payment details
        $approvedOnsiteRequests = OnsiteRequest::where("payment_approved", true)
            ->with(["requestItems.document", "student", "accountingApprover"])
            ->orderBy("payment_approved_at", "desc")
            ->get();

        // Get approved student requests with payment details
        $approvedStudentRequests = StudentRequest::where("payment_approved", true)
            ->with(["requestItems.document", "student.user", "approvedByAccounting"])
            ->orderBy("payment_approved_at", "desc")
            ->get();

        // Calculate total cost for onsite requests
        $approvedOnsiteRequests->transform(function ($request) {
            $totalCost = 0;
            if ($request->requestItems) {
                foreach ($request->requestItems as $item) {
                    $price = $item->document->price ?? 0;
                    $quantity = $item->quantity ?? 1;
                    $totalCost += $price * $quantity;
                }
            }
            $request->calculated_total_cost = $totalCost;
            return $request;
        });

        return view("accounting.history", compact("approvedOnsiteRequests", "approvedStudentRequests"));
    }

    public function manageQRCodes()
    {
        // Get onsite requests in payment step so accounting can set QR code before receipt upload
        $pendingOnsiteRequests = OnsiteRequest::where("current_step", "payment")
            ->where("payment_approved", false)
            ->where("status", "registrar_approved")
            ->with(["requestItems.document", "student"])
            ->orderBy("created_at", "asc")
            ->get();

        // Get student requests awaiting payment confirmation
        $pendingStudentRequests = StudentRequest::where("status", "registrar_approved")
            ->where("payment_approved", false)
            ->with(["requestItems.document", "student.user"])
            ->orderBy("created_at", "asc")
            ->get();

        $defaultQrUrl = $this->getDefaultQrUrl();

        return view("accounting.qr-manage", compact("pendingOnsiteRequests", "pendingStudentRequests", "defaultQrUrl"));
    }

    public function uploadDefaultQRCode(Request $request)
    {
        $request->validate([
            'default_qr_code' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        try {
            $imagesPath = public_path('images');
            if (!is_dir($imagesPath)) {
                mkdir($imagesPath, 0755, true);
            }

            // Remove old default QR files with common extensions.
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
                $oldFile = $imagesPath . DIRECTORY_SEPARATOR . "qr-display.{$ext}";
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $extension = strtolower($request->file('default_qr_code')->getClientOriginalExtension());
            $fileName = "qr-display.{$extension}";
            $request->file('default_qr_code')->move($imagesPath, $fileName);

            return redirect()->route('accounting.qr.manage')->with('success', 'Default payment QR code updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting.qr.manage')->with('error', 'Failed to update default payment QR code.');
        }
    }

    private function getDefaultQrUrl(): string
    {
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
            if (file_exists(public_path("images/qr-display.{$ext}"))) {
                return asset("images/qr-display.{$ext}");
            }
        }

        return asset('images/qr-display.jpg');
    }

    public function uploadQRCode(Request $request, $type, $id)
    {
        // Validate request
        $request->validate([
            'qr_code' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Get the request based on type
            if ($type === 'onsite') {
                $requestModel = OnsiteRequest::findOrFail($id);
            } elseif ($type === 'student') {
                $requestModel = StudentRequest::findOrFail($id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type'
                ], 400);
            }

            // Delete old QR code file if exists
            if ($requestModel->qr_code_path) {
                $oldPath = str_replace('/storage/', '', $requestModel->qr_code_path);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Store the new QR code
            $path = $request->file('qr_code')->store('qr-codes/' . $type, 'public');

            // Update the model
            $requestModel->qr_code_path = Storage::url($path);
            $requestModel->save();

            return response()->json([
                'success' => true,
                'message' => 'QR code uploaded successfully',
                'qr_code_url' => $requestModel->qr_code_path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading QR code: ' . $e->getMessage()
            ], 500);
        }
    }
}

