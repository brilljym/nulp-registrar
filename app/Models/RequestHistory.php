<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestHistory extends Model
{
	use HasFactory;

	protected $fillable = [
		'requestable_id',
		'requestable_type',
		'user_id',
		'action',
		'notes',
	];

	public function requestable()
	{
		return $this->morphTo();
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
