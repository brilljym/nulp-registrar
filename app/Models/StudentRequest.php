<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'reference_no',
        'status',
        'reason',
        'remarks',
        'total_cost',
        'payment_confirmed',
        'registrar_approved',
        'approved_by_registrar_id',
        'registrar_approved_at',
        'expected_release_date',
        'payment_receipt_path',
        'payment_approved',
        'approved_by_accounting_id',
        'payment_approved_at',
        'queue_number',
        'window_id',
        'assigned_registrar_id',
    ];

    protected $casts = [
        'expected_release_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requestItems()
    {
        return $this->hasMany(StudentRequestItem::class);
    }

    public function assignedRegistrar()
    {
        return $this->belongsTo(User::class, 'assigned_registrar_id');
    }

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

    public static function generateReferenceNumber()
    {
        return 'SR-' . now()->format('Ymd') . '-' . str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
