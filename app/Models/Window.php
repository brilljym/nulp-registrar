<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Window extends Model
{
    protected $fillable = [
        'name',
        'window_number',
        'is_occupied',
        'registrar_id',
    ];

    public function assignedRequest()
    {
        return $this->hasOne(OnsiteRequest::class, 'window_id');
    }

    public function onsiteRequests()
    {
        return $this->hasMany(OnsiteRequest::class, 'window_id');
    }

    public function studentRequests()
    {
        return $this->hasMany(StudentRequest::class, 'window_id');
    }

    public function registrar()
    {
        return $this->belongsTo(Registrar::class, 'registrar_id');
    }
}
