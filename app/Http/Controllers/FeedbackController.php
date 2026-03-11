<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\OnsiteRequest;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form for a completed onsite request
     */
    public function show(OnsiteRequest $onsiteRequest)
    {
        // Only allow feedback for completed requests
        if ($onsiteRequest->current_step !== 'completed') {
            return redirect()->back()->with('error', 'Feedback can only be provided for completed requests.');
        }

        // Check if feedback already exists
        if ($onsiteRequest->feedback) {
            return redirect()->back()->with('info', 'Feedback has already been provided for this request.');
        }

        return view('onsite.feedback', compact('onsiteRequest'));
    }

    /**
     * Store the feedback for an onsite request
     */
    public function store(Request $request, OnsiteRequest $onsiteRequest)
    {
        // Only allow feedback for completed requests
        if ($onsiteRequest->current_step !== 'completed') {
            return redirect()->back()->with('error', 'Feedback can only be provided for completed requests.');
        }

        // Check if feedback already exists
        if ($onsiteRequest->feedback) {
            return redirect()->back()->with('info', 'Feedback has already been provided for this request.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Create the feedback
        Feedback::create([
            'onsite_request_id' => $onsiteRequest->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'full_name' => $onsiteRequest->full_name,
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}
