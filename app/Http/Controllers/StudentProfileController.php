<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Ensure we pass a Student model to the view to avoid null property access in Blade.
        // If the authenticated user doesn't have a student profile yet, provide an empty Student instance.
        $student = $user->student;
        if (!$student) {
            $student = new Student();
            // prefill user_id so forms and bindings can use it if needed
            $student->user_id = $user->id;
        }

        return view('student.profile', compact('student', 'user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'course'        => 'required|string|max:255',
            'year_level'    => 'required|string|max:255',
            'department'    => 'required|string|max:255',
            'mobile_number' => 'required|string|max:255',
            'region'        => 'required|string|max:255',
            'province'      => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'barangay'      => 'required|string|max:255',
            'street'        => 'required|string|max:255',
            'house_number'  => 'nullable|string|max:255',
            'block_number'  => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $data = $request->only([
            'course', 'year_level', 'department', 'mobile_number',
            'region', 'province', 'city', 'barangay', 'street',
            'house_number', 'block_number',
        ]);

        $student = $user->student;
        if (!$student) {
            // Create a new student profile linked to this user
            $data['user_id'] = $user->id;
            $student = Student::create($data);
        } else {
            $student->update($data);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
