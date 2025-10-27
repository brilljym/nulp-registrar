<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\StudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RealTimeNotificationService;

class DocumentRequestController extends Controller
{
    protected $notificationService;

    public function __construct(RealTimeNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function showRequestForm()
    {
        $documents = Document::where('is_active', true)->get();

        $pendingRequest = null;
        if (Auth::check() && Auth::user()->student) {
            $pendingRequest = StudentRequest::with(['student.user', 'requestItems.document'])
                ->where('student_id', Auth::user()->student->id)
                ->whereNotIn('status', ['completed'])
                ->latest()
                ->first();
        }

        return view('student.request_document', compact('documents', 'pendingRequest'));
    }

    public function submitRequest(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If the authenticated user doesn't have a student profile, create a minimal one so the request can proceed.
        $user = Auth::user();
    if (!$user->student) {
            // create a minimal student profile; fields can be updated later via the profile page
            // use empty strings for fields that are NOT NULL in the database to avoid integrity errors
            $student = $user->student()->create([
                'student_id' => \App\Models\Student::generateStudentId(),
                'course' => 'Unknown',
                'year_level' => '1',
                'department' => 'Unknown',
                'mobile_number' => '',
                'region' => '',
                'province' => '',
                'city' => '',
                'barangay' => '',
                'street' => '',
                'house_number' => '',
                'block_number' => '',
            ]);
        }

        // Ensure the $user model has the freshly created student relation loaded so it's available below
        $user->load('student');

        $existingRequest = StudentRequest::where('student_id', $user->student->id)
            ->whereNotIn('status', ['completed'])
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending document request. Please wait for it to be completed before submitting a new one.');
        }

        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*.document_id' => 'required|exists:documents,id',
            'documents.*.quantity' => 'required|integer|min:1|max:10',
            'reason' => 'required|string|max:500',
        ]);

        $documents = $request->documents;

        if (empty($documents)) {
            return redirect()->back()->with('error', 'Please select at least one document.');
        }

        $totalCost = 0;
        foreach ($documents as $doc) {
            $document = Document::find($doc['document_id']);
            if ($document) {
                $totalCost += $document->price * $doc['quantity'];
            }
        }

        $studentRequest = StudentRequest::create([
            'student_id' => Auth::user()->student->id,
            'reference_no' => StudentRequest::generateReferenceNumber(),
            'status' => 'pending',
            'reason' => $request->reason,
            'total_cost' => $totalCost,
            'expected_release_date' => now()->addDays(rand(3, 5)),
        ]);

        foreach ($documents as $doc) {
            $document = Document::find($doc['document_id']);
            if ($document) {
                $studentRequest->requestItems()->create([
                    'document_id' => $doc['document_id'],
                    'quantity' => $doc['quantity'],
                    'price' => $document->price,
                ]);
            }
        }

        $studentRequest->load(['student.user', 'requestItems.document']);

        $totalQuantity = array_sum(array_column($documents, 'quantity'));

        $documentNames = $studentRequest->requestItems->map(function ($item) {
            return $item->document->type_document . ' (x' . $item->quantity . ')';
        })->join(', ');

        // Send notification
        $this->notificationService->sendNotification(
            "New student document request: {$studentRequest->reference_no}",
            'new-request',
            [
                'request_id' => $studentRequest->id,
                'reference_no' => $studentRequest->reference_no,
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'student_id' => $studentRequest->student->student_id,
                'document_type' => $documentNames,
                'quantity' => $totalQuantity,
                'status' => $studentRequest->status,
                'total_cost' => $studentRequest->total_cost,
                'created_at' => $studentRequest->created_at->toISOString(),
                'request_type' => 'student',
                'price' => $studentRequest->total_cost,
            ],
            ['registrar-notifications', 'new-student-requests']
        );

        return redirect()->back()->with('success', 'Document request submitted successfully! You can track its progress here until completion.');
    }

    public function track($referenceNo)
    {
        $studentRequest = StudentRequest::with(['student.user', 'requestItems.document', 'assignedRegistrar'])
            ->where('reference_no', $referenceNo)
            ->whereHas('student', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->firstOrFail();

        $steps = [
            ['label' => 'Request Submitted', 'icon' => '📝', 'step' => 'pending'],
            ['label' => 'Registrar Approval', 'icon' => '✅', 'step' => 'registrar_approved'],
            ['label' => 'Payment Processing', 'icon' => '💳', 'step' => 'payment_processing'],
            ['label' => 'Under Review', 'icon' => '🔍', 'step' => 'processing'],
            ['label' => 'Ready', 'icon' => '📦', 'step' => 'ready_for_release'],
            ['label' => 'Completed', 'icon' => '✅', 'step' => 'completed'],
        ];

        $currentStepIndex = 0;
        switch ($studentRequest->status) {
            case 'pending':
                $currentStepIndex = 0;
                break;
            case 'registrar_approved':
                $currentStepIndex = $studentRequest->payment_confirmed || $studentRequest->total_cost == 0 ? 2 : 1;
                break;
            case 'processing':
                $currentStepIndex = 3;
                break;
            case 'ready_for_release':
                $currentStepIndex = 4;
                break;
            case 'completed':
                $currentStepIndex = 5;
                break;
        }

        return view('student.track', compact('studentRequest', 'steps', 'currentStepIndex'));
    }

    public function myRequests()
    {
        $requests = StudentRequest::with(['requestItems.document'])
            ->whereHas('student', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.my-requests', compact('requests'));
    }

    public function showPublicTrack()
    {
        return view('public.track');
    }

    public function publicTrackSearch(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|regex:/^SR-\d{8}-\d{4}$/',
        ], [
            'reference.regex' => 'Please enter a valid reference number format (SR-YYYYMMDD-XXXX).',
        ]);

        $referenceNo = $request->reference;

        $studentRequest = StudentRequest::with(['student.user', 'requestItems.document', 'assignedRegistrar'])
            ->where('reference_no', $referenceNo)
            ->first();

        if (!$studentRequest) {
            return redirect()->back()->with('error', 'Request not found. Please check your reference number and try again.');
        }

        $steps = [
            ['label' => 'Request Submitted', 'icon' => '📝', 'step' => 'pending'],
            ['label' => 'Registrar Approval', 'icon' => '✅', 'step' => 'registrar_approved'],
            ['label' => 'Payment Processing', 'icon' => '💳', 'step' => 'payment_processing'],
            ['label' => 'Under Review', 'icon' => '🔍', 'step' => 'processing'],
            ['label' => 'Ready', 'icon' => '📦', 'step' => 'ready_for_release'],
            ['label' => 'Completed', 'icon' => '✅', 'step' => 'completed'],
        ];

        $currentStepIndex = 0;
        switch ($studentRequest->status) {
            case 'pending':
                $currentStepIndex = 0;
                break;
            case 'registrar_approved':
                $currentStepIndex = $studentRequest->payment_confirmed || $studentRequest->total_cost == 0 ? 2 : 1;
                break;
            case 'processing':
                $currentStepIndex = 3;
                break;
            case 'ready_for_release':
                $currentStepIndex = 4;
                break;
            case 'completed':
                $currentStepIndex = 5;
                break;
        }

        return view('public.track-result', compact('studentRequest', 'steps', 'currentStepIndex'));
    }

    public function downloadApk(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => 'required|string',
            'apk_file' => 'required|string',
        ]);

        $studentRequest = StudentRequest::where('reference_no', $validated['reference_no'])->first();

        if (!$studentRequest) {
            return redirect()->back()->with('apk_error', 'Invalid reference number. Please check and try again.');
        }

        if (!Auth::check() || !Auth::user()->student || Auth::user()->student->id !== $studentRequest->student_id) {
            return redirect()->back()->with('apk_error', 'This reference number does not belong to your account.');
        }

        $apkPath = public_path('apk/' . $validated['apk_file']);

        if (!file_exists($apkPath)) {
            return redirect()->back()->with('apk_error', 'APK file not found. Please contact support.');
        }

        if (pathinfo($apkPath, PATHINFO_EXTENSION) !== 'apk') {
            return redirect()->back()->with('apk_error', 'Invalid file type.');
        }

        $originalFilename = $validated['apk_file'];

        return response()->download($apkPath, $originalFilename, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="' . $originalFilename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function confirmPayment(Request $request)
    {
        if (!Auth::check() || !Auth::user()->student) {
            return redirect()->route('student.profile')->with('error', 'Student profile not found. Please complete your profile first.');
        }

        $pendingRequest = StudentRequest::with(['student.user', 'requestItems.document'])
            ->where('student_id', Auth::user()->student->id)
            ->where('status', 'pending')
            ->where('payment_confirmed', false)
            ->where('total_cost', '>', 0)
            ->first();

        if (!$pendingRequest) {
            return redirect()->back()->with('error', 'No pending request found that requires payment confirmation.');
        }

        $pendingRequest->update([
            'payment_confirmed' => true,
            'status' => 'processing',
            'updated_at' => now(),
        ]);

        $documentNames = $pendingRequest->requestItems->map(function ($item) {
            return $item->document->type_document . ' (x' . $item->quantity . ')';
        })->join(', ');

        $this->notificationService->sendNotification(
            "Student confirmed payment for request #{$pendingRequest->reference_no}",
            'payment_confirmed',
            [
                'request_id' => $pendingRequest->id,
                'reference_no' => $pendingRequest->reference_no,
                'student_name' => $pendingRequest->student->user->first_name . ' ' . $pendingRequest->student->user->last_name,
                'student_id' => $pendingRequest->student->student_id,
                'document_type' => $documentNames,
                'quantity' => $pendingRequest->requestItems->sum('quantity'),
                'status' => $pendingRequest->status,
                'status_update' => true,
                'total_cost' => $pendingRequest->total_cost,
                'created_at' => $pendingRequest->created_at->toISOString(),
                'request_type' => 'student',
                'price' => $pendingRequest->total_cost,
            ],
            ['registrar-notifications']
        );

        return redirect()->back()->with('success', 'Payment confirmed! Your request is now being processed.');
    }

    public function uploadReceipt(Request $request, StudentRequest $studentRequest)
    {
        if (!Auth::check() || !Auth::user()->student || Auth::user()->student->id !== $studentRequest->student_id) {
            return redirect()->back()->with('error', 'Unauthorized access to this request or student profile not found.');
        }

        if ($studentRequest->status !== 'registrar_approved' || $studentRequest->total_cost == 0 || $studentRequest->payment_receipt_path) {
            return redirect()->back()->with('error', 'This request is not eligible for receipt upload.');
        }

        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $receiptPath = $request->file('payment_receipt')->store('receipts', 'public');

        $studentRequest->update([
            'payment_receipt_path' => $receiptPath,
        ]);

        $documentNames = $studentRequest->requestItems->map(function ($item) {
            return $item->document->type_document . ' (x' . $item->quantity . ')';
        })->join(', ');

        $this->notificationService->sendNotification(
            "Payment receipt uploaded for student document request {$studentRequest->reference_no}",
            'receipt-uploaded',
            [
                'request_id' => $studentRequest->id,
                'reference_no' => $studentRequest->reference_no,
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'student_id' => $studentRequest->student->student_id,
                'document_type' => $documentNames,
                'total_cost' => $studentRequest->total_cost,
                'request_type' => 'student',
            ],
            ['accounting-notifications']
        );

        return redirect()->back()->with('success', 'Payment receipt uploaded successfully! It will be reviewed by accounting.');
    }

}
