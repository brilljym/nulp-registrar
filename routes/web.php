<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\RegistrarOnsiteController; // ✅ Added this
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\OnsiteRequestController;
use App\Http\Controllers\FeedbackController; // ✅ Added for feedback functionality
use App\Http\Controllers\WindowController; // ✅ Already added
use App\Http\Controllers\TwoFactorController;

// Home/Splash Screen Route
// Temporary route to create test onsite requests
Route::get('/create-test-onsite-requests', function () {
    try {
        $document = \App\Models\Document::first();
        if (!$document) {
            return response()->json(['error' => 'No documents found in database']);
        }

        $testRequests = [
            [
                'ref_code' => 'NU001A001',
                'queue_number' => 'A001',
                'full_name' => 'Juan Dela Cruz',
                'student_id' => null, // No student ID constraint
                'course' => 'BS Computer Science',
                'year_level' => '3rd Year',
                'department' => 'College of Computer Studies',
                'document_id' => $document->id,
                'quantity' => 1,
                'reason' => 'For job application',
                'status' => 'ready_for_pickup',
                'current_step' => 'completed',
                'expected_release_date' => now()->addDays(1),
            ],
            [
                'ref_code' => 'NU001A002',
                'queue_number' => 'A002',
                'full_name' => 'Maria Santos',
                'student_id' => null,
                'course' => 'BS Information Technology',
                'year_level' => '2nd Year',
                'department' => 'College of Computer Studies',
                'document_id' => $document->id,
                'quantity' => 2,
                'reason' => 'For scholarship application',
                'status' => 'processing',
                'current_step' => 'processing',
                'expected_release_date' => now()->addDays(3),
            ],
            [
                'ref_code' => 'NU001A003',
                'queue_number' => 'A003',
                'full_name' => 'Pedro Reyes',
                'student_id' => null,
                'course' => 'BS Business Administration',
                'year_level' => '4th Year',
                'department' => 'College of Business',
                'document_id' => $document->id,
                'quantity' => 1,
                'reason' => 'For internship',
                'status' => 'in_queue',
                'current_step' => 'waiting',
                'expected_release_date' => now()->addDays(5),
            ],
            [
                'ref_code' => 'NU001A004',
                'queue_number' => 'A004',
                'full_name' => 'Ana Garcia',
                'student_id' => null,
                'course' => 'BS Psychology',
                'year_level' => '1st Year',
                'department' => 'College of Arts and Sciences',
                'document_id' => $document->id,
                'quantity' => 1,
                'reason' => 'For medical requirements',
                'status' => 'accepted',
                'current_step' => 'payment',
                'expected_release_date' => now()->addDays(2),
            ],
        ];

        $created = [];
        foreach ($testRequests as $requestData) {
            $existing = \App\Models\OnsiteRequest::where('queue_number', $requestData['queue_number'])->first();
            if ($existing) {
                $created[] = "Queue number {$requestData['queue_number']} already exists";
                continue;
            }

            $request = \App\Models\OnsiteRequest::create($requestData);
            $created[] = "Created queue number: {$request->queue_number}";
        }

        return response()->json([
            'success' => true,
            'message' => 'Test onsite requests created',
            'created' => $created,
            'available_queue_numbers' => ['A001', 'A002', 'A003', 'A004']
        ]);

    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/auth/login', [AuthController::class, 'showLoginFormPage'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

// Dashboard Route (Role-Based Redirection)
Route::get('/dashboard', function () {
    $roleName = Str::lower(Auth::user()->role->name);

    return match ($roleName) {
        'admin' => redirect()->route('admin.users.index'),
        'registrar' => redirect()->route('registrar.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        'accounting' => redirect()->route('accounting.dashboard'),
        default => abort(403, 'Unauthorized role.'),
    };
})->middleware('auth');

// Admin-only: User Management
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::get('/users/{user}/edit-modal', [UserController::class, 'editModal'])->name('users.edit-modal');

    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class)->only(['index', 'show', 'destroy']);
        // Admin registrars and documents
    Route::resource('registrars', \App\Http\Controllers\Admin\RegistrarController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
    
    // Additional document routes
    Route::post('documents/{id}/toggle-status', [\App\Http\Controllers\Admin\DocumentController::class, 'toggleStatus'])->name('documents.toggle-status');
    Route::get('documents/{id}/stats', [\App\Http\Controllers\Admin\DocumentController::class, 'getStats'])->name('documents.stats');
    
    // Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');
    Route::get('reports/chart-data', [\App\Http\Controllers\Admin\ReportsController::class, 'getChartDataJson'])->name('reports.chart-data');
    Route::get('reports/print', [\App\Http\Controllers\Admin\ReportsController::class, 'printReport'])->name('reports.print');
    
    // PIA & Stakeholder Reports
    Route::get('pia-reports', [\App\Http\Controllers\Admin\PIAReportsController::class, 'index'])->name('pia.reports');
    Route::get('pia-reports/export-compliance', [\App\Http\Controllers\Admin\PIAReportsController::class, 'exportCompliance'])->name('pia.export-compliance');
    Route::get('pia-reports/export-operational', [\App\Http\Controllers\Admin\PIAReportsController::class, 'exportOperational'])->name('pia.export-operational');
});

// Student Routes
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    // 2FA Routes (exempt from OTP verification middleware)
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/verify', [TwoFactorController::class, 'showVerifyForm'])->name('verify');
        Route::post('/send-otp', [TwoFactorController::class, 'sendOtp'])->name('send-otp');
        Route::post('/verify-otp', [TwoFactorController::class, 'verifyOtp'])->name('verify-otp');
        Route::post('/toggle', [TwoFactorController::class, 'toggle'])->name('toggle');
        Route::get('/status', [TwoFactorController::class, 'getStatus'])->name('status');
    });

    // Protected student routes (require OTP verification if 2FA is enabled)
    Route::middleware(['auth', \App\Http\Middleware\RequireOtpVerification::class])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [StudentProfileController::class, 'update'])->name('profile.update');

        Route::get('/request-document', [DocumentRequestController::class, 'showRequestForm'])->name('request.document');
        Route::post('/request-document', [DocumentRequestController::class, 'submitRequest'])->name('request.document.submit');
        Route::post('/confirm-payment', [DocumentRequestController::class, 'confirmPayment'])->name('confirm.payment');
        Route::post('/upload-receipt/{studentRequest}', [DocumentRequestController::class, 'uploadReceipt'])->name('upload.receipt');
        Route::post('/download-apk', [DocumentRequestController::class, 'downloadApk'])->name('download.apk');
        Route::get('/download/{request}', [StudentDashboardController::class, 'download'])->name('request.download');
        
        // ✅ Request Tracking Routes
        Route::get('/track/{referenceNo}', [DocumentRequestController::class, 'track'])->name('track');
        Route::get('/my-requests', [DocumentRequestController::class, 'myRequests'])->name('my-requests');
    });
});

// Registrar Routes
Route::middleware(['auth'])->prefix('registrar')->name('registrar.')->group(function () {
    // 2FA Routes for Registrars (exempt from OTP verification middleware)
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/verify', [TwoFactorController::class, 'showVerifyForm'])->name('verify');
        Route::post('/send-otp', [TwoFactorController::class, 'sendOtp'])->name('send-otp');
        Route::post('/verify-otp', [TwoFactorController::class, 'verifyOtp'])->name('verify-otp');
        Route::post('/toggle', [TwoFactorController::class, 'toggle'])->name('toggle');
        Route::get('/status', [TwoFactorController::class, 'getStatus'])->name('status');
    });

    // Protected registrar routes (require OTP verification if 2FA is enabled)
    Route::middleware(['auth', \App\Http\Middleware\RequireOtpVerification::class])->group(function () {
        Route::get('/dashboard', [RegistrarController::class, 'index'])->name('dashboard');
        Route::get('/pending', [RegistrarController::class, 'pending'])->name('pending');
        Route::get('/completed', [RegistrarController::class, 'completed'])->name('completed');
        Route::post('/upload/{studentRequest}', [RegistrarController::class, 'upload'])->name('upload');
        Route::get('/download/{studentRequest}', [RegistrarController::class, 'download'])->name('download');
        
        // ✅ Student Transaction Workflow Actions
        Route::post('/approve/{studentRequest}', [RegistrarController::class, 'approveRequest'])->name('approve');
        Route::post('/reject/{studentRequest}', [RegistrarController::class, 'rejectRequest'])->name('reject');
        Route::post('/release/{studentRequest}', [RegistrarController::class, 'markAsReadyForRelease'])->name('release');
        Route::post('/ready-pickup/{studentRequest}', [RegistrarController::class, 'markAsReadyForPickup'])->name('ready-pickup');
        Route::post('/close/{studentRequest}', [RegistrarController::class, 'closeRequest'])->name('close');
        Route::post('/complete/{studentRequest}', [RegistrarController::class, 'markAsCompleted'])->name('complete');
        Route::post('/update-release-date/{studentRequest}', [RegistrarController::class, 'updateExpectedReleaseDate'])->name('update-release-date');

        // ✅ Onsite Transactions Tab (Registrar View)
        Route::get('/onsite', [RegistrarOnsiteController::class, 'index'])->name('onsite.index');
        Route::post('/onsite/approve/{onsiteRequest}', [RegistrarOnsiteController::class, 'approveRequest'])->name('onsite.approve');
        Route::post('/onsite/reject/{onsiteRequest}', [RegistrarOnsiteController::class, 'rejectRequest'])->name('onsite.reject');
        Route::post('/onsite/take/{onsiteRequest}', [RegistrarOnsiteController::class, 'takeRequest'])->name('onsite.take');
        Route::post('/onsite/ready-pickup/{onsiteRequest}', [RegistrarOnsiteController::class, 'readyForPickup'])->name('registrar.onsite.ready-pickup');
        Route::post('/onsite/complete-request/{onsiteRequest}', [RegistrarOnsiteController::class, 'completeRequest'])->name('onsite.complete-request');

        // Layout expects these friendly named routes — point them to the same controllers
        Route::get('/requests', [RegistrarController::class, 'index'])->name('all-requests');
        Route::get('/onsite/management', [RegistrarOnsiteController::class, 'index'])->name('onsite.management');
        Route::get('/onsite/completed', [RegistrarOnsiteController::class, 'index'])->name('onsite.completed');
        Route::get('/onsite/pending', [RegistrarOnsiteController::class, 'index'])->name('onsite.pending');

        // Management aliases referenced by layout
        Route::get('/analytics', [RegistrarController::class, 'analytics'])->name('analytics');

        // ✅ Onsite Actions by Registrar
        Route::post('/onsite/release/{id}', [OnsiteRequestController::class, 'markAsReadyForRelease'])->name('onsite.release');
        Route::post('/onsite/close/{id}', [OnsiteRequestController::class, 'closeRequest'])->name('onsite.close');
        Route::post('/onsite/complete/{id}', [OnsiteRequestController::class, 'markAsCompleted'])->name('onsite.complete');
        Route::post('/onsite/ready-pickup/{id}', [OnsiteRequestController::class, 'markAsReadyForPickup'])->name('onsite.ready-pickup');
        Route::post('/onsite/update-release-date/{id}', [OnsiteRequestController::class, 'updateExpectedReleaseDate'])->name('onsite.update-release-date');

        // ✅ Window Queue Management
        Route::get('/windows', [WindowController::class, 'index'])->name('windows');
        Route::post('/windows/{window}/toggle', [WindowController::class, 'toggleOccupied'])->name('windows.toggle');
        
        // ✅ Queue Display
        Route::get('/queue/display', [WindowController::class, 'queueDisplay'])->name('queue.display');
        
        // ✅ Queue In Progress
        Route::get('/queue/in-progress', [RegistrarController::class, 'getQueueInProgress'])->name('queue.in-progress');
    });
});

// ✅ Accounting Routes
Route::middleware(['auth'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AccountingController::class, 'index'])->name('dashboard');
    Route::get('/history', [App\Http\Controllers\AccountingController::class, 'history'])->name('history');
    Route::post('/approve/onsite/{onsiteRequest}', [App\Http\Controllers\AccountingController::class, 'approveOnsite'])->name('approve.onsite');
    Route::post('/approve/student/{studentRequest}', [App\Http\Controllers\AccountingController::class, 'approveStudent'])->name('approve.student');
    Route::post('/reject/onsite/{onsiteRequest}', [App\Http\Controllers\AccountingController::class, 'rejectOnsite'])->name('reject.onsite');
    Route::post('/reject/student/{studentRequest}', [App\Http\Controllers\AccountingController::class, 'rejectStudent'])->name('reject.student');
    Route::get('/receipt/onsite/{onsiteRequest}', [App\Http\Controllers\AccountingController::class, 'viewOnsiteReceipt'])->name('receipt.onsite');
    Route::get('/receipt/student/{studentRequest}', [App\Http\Controllers\AccountingController::class, 'viewStudentReceipt'])->name('receipt.student');
});


// ✅ Onsite Transaction (Public - No Login Required)
Route::get('/onsite-request', [OnsiteRequestController::class, 'index'])->name('onsite.index');
Route::post('/onsite-request', [OnsiteRequestController::class, 'store'])->name('onsite.store');
Route::put('/onsite-request/{onsiteRequest}', [OnsiteRequestController::class, 'update'])->name('onsite.update');
Route::get('/onsite/submit-reference', function () {
    return redirect()->route('onsite.index')->with('error', 'Please submit the reference code through the proper form.');
});
Route::post('/onsite/submit-reference', [OnsiteRequestController::class, 'submitReference'])->name('onsite.reference.submit');
Route::get('/onsite-request/{onsiteRequest}/timeline', [OnsiteRequestController::class, 'timeline'])->name('onsite.timeline');
Route::post('/onsite-request/{onsiteRequest}/upload-receipt', [OnsiteRequestController::class, 'uploadReceipt'])->name('onsite.upload.receipt');

// ✅ Feedback Routes (Public - No Login Required)
Route::get('/onsite-request/{onsiteRequest}/feedback', [App\Http\Controllers\FeedbackController::class, 'show'])->name('onsite.feedback.show');
Route::post('/onsite-request/{onsiteRequest}/feedback', [App\Http\Controllers\FeedbackController::class, 'store'])->name('onsite.feedback.store');

// ✅ Public Document Request Tracking (No Login Required)
Route::get('/track', [DocumentRequestController::class, 'showPublicTrack'])->name('public.track');
Route::get('/track/search', [DocumentRequestController::class, 'publicTrackSearch'])->name('public.track.search');

// Autofill Student Search API moved to routes/api.php

// Pusher Test Routes
Route::get('/test-pusher', function () {
    try {
        // Test broadcasting an event
        $service = new \App\Services\RealTimeNotificationService();
        $service->sendSuccess('Pusher test message sent successfully!', [
            'test' => true,
            'time' => now()->format('Y-m-d H:i:s')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Test notification broadcasted successfully!',
            'pusher_config' => [
                'app_id' => config('broadcasting.connections.pusher.app_id'),
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to broadcast: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/test-onsite-notification', function () {
    try {
        $service = new \App\Services\RealTimeNotificationService();
        $service->sendNotification(
            "Test new onsite request submitted: NUTEST123",
            'new-request',
            [
                'request_id' => 999,
                'ref_code' => 'NUTEST123',
                'student_name' => 'Test Student',
                'document_type' => 'Test Document',
                'course' => 'Test Course',
                'year_level' => 'Test Year',
                'department' => 'Test Department',
                'quantity' => 1,
                'current_step' => 'payment',
                'created_at' => now()->toISOString(),
                'request_type' => 'onsite',
                'price' => 100
            ],
            ['registrar-notifications', 'new-onsite-requests']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Test onsite notification sent successfully!',
            'channels' => ['registrar-notifications', 'new-onsite-requests']
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test notification: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/pusher-demo', function () {
    return view('pusher-demo');
});

// Test route for request status updates
Route::get('/test-request-update/{requestId?}', function ($requestId = 'TEST123') {
    try {
        $service = new \App\Services\RealTimeNotificationService();
        
        // Test request status update notification
        $service->sendRequestStatusUpdate(
            $requestId,
            'processing',
            "Test status update for request {$requestId}",
            [
                'student_name' => 'John Doe',
                'document_type' => 'Transcript of Records',
                'registrar_name' => 'Jane Smith',
                'request_type' => 'onsite'
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Request status update broadcasted for {$requestId}!",
            'channels' => [
                'registrar-notifications',
                "request-{$requestId}"
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to broadcast: ' . $e->getMessage()
        ], 500);
    }
});




// Kiosk Routes (Public - No Login Required)
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [App\Http\Controllers\KioskController::class, 'index'])->name('index');
    Route::post('/confirm', [App\Http\Controllers\KioskController::class, 'confirm'])->name('confirm');
    Route::post('/confirm-pickup/{type}/{id}', [App\Http\Controllers\KioskController::class, 'confirmPickup'])->name('confirm-pickup');
    Route::get('/queue-status', [App\Http\Controllers\KioskController::class, 'queueStatus'])->name('queue-status');
    Route::post('/print-receipt/{type}/{id}', [App\Http\Controllers\KioskController::class, 'printReceipt'])->name('print-receipt');
    Route::post('/test-printer', [App\Http\Controllers\KioskController::class, 'testPrinter'])->name('test-printer');
    Route::get('/status/{queueNumber}', [App\Http\Controllers\KioskController::class, 'status'])->name('status');
    Route::get('/queue/{kioskNumber}', [App\Http\Controllers\KioskController::class, 'showKioskQueue'])->name('queue');
});

// Public Queue Display (No Login Required)
Route::get('/queue-display', [App\Http\Controllers\WindowController::class, 'queueDisplay'])->name('queue.public.display');

// QR Code Verification Route (Public - No Login Required)
Route::get('/verify/{referenceCode}', function($referenceCode) {
    try {
        // Try to find the request in both student and onsite tables
        $studentRequest = \App\Models\StudentRequest::where('reference_no', $referenceCode)->first();
        $onsiteRequest = \App\Models\OnsiteRequest::where('ref_code', $referenceCode)->first();
        
        if ($studentRequest) {
            $request = $studentRequest;
            $type = 'student';
            $name = $request->student->user->first_name . ' ' . $request->student->user->last_name;
        } elseif ($onsiteRequest) {
            $request = $onsiteRequest;
            $type = 'onsite';
            $name = $request->full_name;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Request not found',
                'reference' => $referenceCode
            ], 404);
        }
        
        // Calculate total amount
        $totalAmount = $request->requestItems->sum(function($item) {
            return $item->quantity * $item->document->price;
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Request verified successfully',
            'data' => [
                'type' => $type,
                'reference' => $referenceCode,
                'queue_number' => $request->queue_number,
                'name' => $name,
                'status' => $request->status,
                'total_amount' => $totalAmount,
                'documents' => $request->requestItems->map(function($item) {
                    return [
                        'document' => $item->document->type_document,
                        'quantity' => $item->quantity,
                        'price' => $item->document->price
                    ];
                }),
                'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $request->updated_at->format('Y-m-d H:i:s')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error verifying request: ' . $e->getMessage(),
            'reference' => $referenceCode
        ], 500);
    }
})->name('verify.request');
