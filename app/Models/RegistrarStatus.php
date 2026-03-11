<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrarStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'window_number',
        'is_available',
        'checked_in_at',
        'checked_out_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
