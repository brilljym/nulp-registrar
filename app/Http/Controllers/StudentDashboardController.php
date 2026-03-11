<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentRequest;
use App\Models\StudentRequestItem;


class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        // Only show completed student requests on dashboard
        try {
            $completedRequests = StudentRequest::with(['requestItems.document'])
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->orderByDesc('updated_at')
                ->get();
        } catch (\Exception $e) {
            // If tables don't exist, return empty collection
            $completedRequests = collect();
        }

        return view('dashboard.student', compact('completedRequests'));
    }
    public function download(StudentRequest $request)
    {
        // Optional: ensure the user owns the request
        if (auth()->user()->id !== $request->student->user_id) {
            abort(403, 'Unauthorized access');
        }

        // For now, file downloads are not implemented in the new structure
        return back()->with('error', 'File download functionality is not yet available for this request type.');
    }
}
