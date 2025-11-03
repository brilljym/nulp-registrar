<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class OnsiteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'full_name',
        'course',
        'year_level',
        'department',
        'email',
        'quantity',
        'reason',           // Added reason field
        'remarks',          // Added remarks field
        'document_id',      // Added document_id field
        'current_step',
        'status',
        'window_id',        // Correct column name for window assignment
        'assigned_registrar_id', // same here for registrar assignment
        'ref_code',   // Corrected to match DB column
        'queue_number', // Added queue number field
        'payment_receipt_path',
        'payment_approved',
        'approved_by_accounting_id',
        'payment_approved_at',
        'expected_release_date',
        'registrar_approved',
        'approved_by_registrar_id',
        'registrar_approved_at',
    ];

    protected $casts = [
        'expected_release_date' => 'datetime',
        'payment_approved_at' => 'datetime',
        'registrar_approved_at' => 'datetime',
    ];

    // Optional relationship if student is matched
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assignedWindow()
    {
        return $this->belongsTo(Window::class, 'window_id');
    }

    // Alias for window relationship (if you prefer shorter name)
    public function window()
    {
        return $this->belongsTo(Window::class, 'window_id');
    }

    public function registrar()
    {
        return $this->belongsTo(User::class, 'assigned_registrar_id');
    }

    public function registrarApprover()
    {
        return $this->belongsTo(User::class, 'approved_by_registrar_id');
    }

    public function accountingApprover()
    {
        return $this->belongsTo(User::class, 'approved_by_accounting_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function requestItems()
    {
        return $this->hasMany(OnsiteRequestItem::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
