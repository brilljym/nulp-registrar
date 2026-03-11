<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedbacks';

    protected $fillable = [
        'onsite_request_id',
        'rating',
        'comment',
        'full_name'
    ];

    /**
     * Get the onsite request that this feedback belongs to.
     */
    public function onsiteRequest()
    {
        return $this->belongsTo(OnsiteRequest::class);
    }
}
