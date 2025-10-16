<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'course',
        'year_level',
        'department',
        'mobile_number',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'house_number',
        'block_number',
    ];

    /**
     * Generate a new unique student ID.
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');
        
        // Find the highest existing student ID for this year
        $lastStudent = self::where('student_id', 'LIKE', $year . '-%')
            ->orderBy('student_id', 'desc')
            ->first();
        
        if ($lastStudent) {
            // Extract the number part and increment
            $parts = explode('-', $lastStudent->student_id);
            $number = (int) $parts[1] + 1;
        } else {
            // Start from 1 if no students exist for this year
            $number = 1;
        }
        
        return sprintf('%s-%06d', $year, $number);
    }

    /**
     * Get the user this student belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentRequests()
    {
        return $this->hasMany(StudentRequest::class);
    }
}
