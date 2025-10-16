<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_type',
        'request_id',
        'queue_number',
        'customer_name',
        'documents',
        'total_cost',
        'qr_data',
        'status',
        'printed_at',
        'printer_name',
        'error_message'
    ];

    protected $casts = [
        'documents' => 'array',
        'printed_at' => 'datetime',
        'total_cost' => 'decimal:2'
    ];

    // Scopes for easy querying
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Relationships (commented out to avoid issues)
    // public function studentRequest()
    // {
    //     return $this->belongsTo(StudentRequest::class, 'request_id')
    //         ->where('request_type', 'student');
    // }

    // public function onsiteRequest()
    // {
    //     return $this->belongsTo(OnsiteRequest::class, 'request_id')
    //         ->where('request_type', 'onsite');
    // }
}
