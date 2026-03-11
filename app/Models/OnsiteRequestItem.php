<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnsiteRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'onsite_request_id',
        'document_id',
        'quantity',
    ];

    public function onsiteRequest()
    {
        return $this->belongsTo(OnsiteRequest::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
