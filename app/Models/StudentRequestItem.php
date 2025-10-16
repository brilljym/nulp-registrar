<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_request_id',
        'document_id',
        'quantity',
        'price',
    ];

    public function studentRequest()
    {
        return $this->belongsTo(StudentRequest::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
