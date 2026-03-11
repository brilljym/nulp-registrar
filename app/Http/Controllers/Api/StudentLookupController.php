<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentLookupController extends Controller
{
    public function search(Request $request)
    {
        $nameQuery = $request->get('name');
        $studentIdQuery = $request->get('student_id');

        if (($nameQuery && strlen($nameQuery) < 2) && (!$studentIdQuery || strlen($studentIdQuery) < 2)) {
            return response()->json([], 200);
        }

        $students = Student::with('user')
            ->when($studentIdQuery, function ($q) use ($studentIdQuery) {
                $q->where('student_id', 'like', "%$studentIdQuery%");
            })
            ->when($nameQuery, function ($q) use ($nameQuery) {
                $q->whereHas('user', function ($uq) use ($nameQuery) {
                    $uq->where(function ($nameQ) use ($nameQuery) {
                        $nameQ->where('first_name', 'like', "%$nameQuery%")
                              ->orWhere('last_name', 'like', "%$nameQuery%")
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$nameQuery%"]);
                    });
                });
            })
            ->limit(8)
            ->get()
            ->map(function ($student) {
                $fullName = trim((optional($student->user)->first_name ?? '') . ' ' . (optional($student->user)->last_name ?? ''));

                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'full_name' => $fullName,
                    'course' => $student->course,
                    'year_level' => $student->year_level,
                    'department' => $student->department,
                ];
            });

        return response()->json($students);
    }
}
