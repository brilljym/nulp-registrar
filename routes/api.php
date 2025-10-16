<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\StudentLookupController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\OnsiteRequestController;
use App\Http\Controllers\TwoFactorController;

// Queue Management API Routes
Route::prefix('queue')->group(function () {
    Route::post('/join', [QueueController::class, 'joinQueue']);
    Route::get('/status', [QueueController::class, 'getQueueStatus']);
    Route::get('/customer/{customerId}', [QueueController::class, 'getCustomerStatus']);
    Route::put('/customer/{customerId}/status', [QueueController::class, 'updateCustomerStatus']);
    Route::delete('/customer/{customerId}', [QueueController::class, 'removeFromQueue']);
    Route::get('/estimate', [QueueController::class, 'getWaitTimeEstimate']);
    Route::get('/analytics', [QueueController::class, 'getAnalytics']);
    Route::get('/next-customer', [QueueController::class, 'getNextCustomer']);
    Route::post('/settings/counters', [QueueController::class, 'updateServiceCounters']);
    Route::get('/health', [QueueController::class, 'healthCheck']);
    Route::get('/real-time-updates', [QueueController::class, 'getRealTimeUpdates']);
});

Route::get('/students/search', [OnsiteRequestController::class, 'searchStudent']);
Route::get('/students/test', [OnsiteRequestController::class, 'testStudentSearch']);

// 2FA Test Route
Route::middleware('auth')->get('/test-2fa', function () {
    $user = Auth::user();
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->school_email,
        'role_id' => $user->role_id,
        'role_name' => $user->role ? $user->role->name : null,
        'two_factor_enabled' => $user->two_factor_enabled,
        'should_redirect_to_2fa' => ($user->role && $user->role->name === 'student' && $user->two_factor_enabled),
        'session_otp_verified' => session('otp_verified', false),
        'session_otp_verified_at' => session('otp_verified_at'),
        'current_route' => request()->route() ? request()->route()->getName() : 'unknown'
    ]);
});

// Clear 2FA Session Route (for debugging)
Route::middleware('auth')->post('/clear-2fa-session', function () {
    session()->forget(['otp_verified', 'otp_verified_at']);
    return response()->json([
        'success' => true,
        'message' => '2FA session data cleared'
    ]);
});

// 2FA Status API Route
Route::middleware('auth')->get('/user-2fa-status', function () {
    $user = Auth::user();
    Log::info('2FA Status Check', [
        'user_id' => $user->id,
        'email' => $user->school_email,
        'role' => $user->role ? $user->role->name : 'no_role',
        'two_factor_enabled' => $user->two_factor_enabled ?? false
    ]);
    
    return response()->json([
        'two_factor_enabled' => $user->two_factor_enabled ?? false,
        'user_role' => $user->role ? $user->role->name : null,
        'debug' => true
    ]);
});

// Debug 2FA Route
Route::middleware('auth')->get('/debug-2fa', function () {
    $user = Auth::user();
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'email' => $user->school_email,
            'role' => $user->role ? $user->role->name : null,
            'two_factor_enabled' => $user->two_factor_enabled,
            'personal_email' => $user->personal_email,
        ],
        'session' => [
            'otp_verified' => session('otp_verified', false),
            'otp_verified_at' => session('otp_verified_at'),
        ],
        'routes' => [
            'toggle_url' => route('student.2fa.toggle'),
            'send_otp_url' => route('student.2fa.send-otp'),
            'verify_url' => route('student.2fa.verify'),
        ]
    ]);
});

// Fallback search with better error handling for production
Route::get('/students/search-simple', function (Illuminate\Http\Request $request) {
    try {
        // Set content type header immediately
        header('Content-Type: application/json');
        
        $query = $request->get('query');
        $searchBy = $request->get('search_by', 'student_id');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([], 200);
        }
        
        $students = collect();
        
        if ($searchBy === 'student_id') {
            // Simple search by student_id
            $students = \App\Models\Student::where('student_id', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();
        } elseif ($searchBy === 'full_name') {
            // Simple search by full name
            $students = \App\Models\Student::whereHas('user', function ($userQuery) use ($query) {
                $userQuery->where(function ($nameQuery) use ($query) {
                    $nameQuery->where('first_name', 'LIKE', "%{$query}%")
                             ->orWhere('last_name', 'LIKE', "%{$query}%");
                });
            })->limit(5)->get();
        }
        
        $results = [];
        foreach ($students as $student) {
            $fullName = 'Student ID: ' . $student->student_id;
            
            // Try to get user info safely
            try {
                if ($student->user_id) {
                    $user = \App\Models\User::find($student->user_id);
                    if ($user) {
                        $firstName = $user->first_name ?? '';
                        $lastName = $user->last_name ?? '';
                        if (!empty($firstName) || !empty($lastName)) {
                            $fullName = trim($firstName . ' ' . $lastName);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Keep default full name
            }
            
            $results[] = [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'full_name' => $fullName,
                'course' => $student->course ?? '',
                'year_level' => $student->year_level ?? '',
                'department' => $student->department ?? '',
            ];
        }
        
        return response()->json($results);
        
    } catch (\Exception $e) {
        // Log error but return valid JSON
        error_log('Simple student search error: ' . $e->getMessage());
        return response()->json(['error' => 'Search failed', 'message' => $e->getMessage()], 500);
    }
});

// Reference ID endpoints
Route::get('/transactions/search', [ReferenceController::class, 'searchTransactions']);
Route::get('/onsite-requests/search', [ReferenceController::class, 'searchOnsiteRequests']);
Route::get('/transactions/reference/{reference}', [ReferenceController::class, 'getTransactionByReference']);
Route::get('/onsite-requests/reference/{refCode}', [ReferenceController::class, 'getOnsiteRequestByReference']);

// Debug endpoint to see transaction statuses
Route::get('/debug/transactions', [ReferenceController::class, 'debugTransactions']);

// Simple test endpoint
Route::get('/test', function() {
    return response()->json(['message' => 'API is working', 'timestamp' => now()]);
});

// Print Job Management API Routes (for local print service)
Route::prefix('print-jobs')->group(function () {
    Route::get('/pending', [App\Http\Controllers\Api\PrintJobController::class, 'getPendingJobs']);
    Route::put('/{jobId}/completed', [App\Http\Controllers\Api\PrintJobController::class, 'markCompleted']);
    Route::put('/{jobId}/failed', [App\Http\Controllers\Api\PrintJobController::class, 'markFailed']);
    Route::get('/status', [App\Http\Controllers\Api\PrintJobController::class, 'getStatus']);
});
