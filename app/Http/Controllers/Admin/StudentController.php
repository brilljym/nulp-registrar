<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user:id,first_name,middle_name,last_name')
                    ->select('id', 'user_id', 'student_id', 'course', 'year_level', 'department')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load('user');
        return view('admin.students.show', compact('student'));
    }

    // Additional CRUD methods can be added later as needed.
}
