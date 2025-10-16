<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The relationships that should always be loaded.
     */
    protected $with = ['role'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            // If this user is a registrar, reset any assigned window requests
            if ($user->registrar) {
                \App\Models\OnsiteRequest::where('assigned_registrar_id', $user->id)
                    ->update([
                        'assigned_registrar_id' => null,
                        'window_id' => null,
                        'status' => 'pending',
                        'current_step' => 'start'
                    ]);
                
                // Log the cleanup
                Log::info("Reset window assignments for deleted registrar user: {$user->id}");
            }
        });
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the student profile if the user is a student.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the registrar profile if the user is a registrar.
     */
    public function registrar()
    {
        return $this->hasOne(\App\Models\Registrar::class);
    }

    /**
     * Get the onsite requests assigned to this registrar.
     */
    public function onsiteRequests()
    {
        return $this->hasMany(\App\Models\OnsiteRequest::class, 'assigned_registrar_id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'school_email',
        'personal_email',
        'password',
        'role_id',
        'two_factor_enabled',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Generate a new OTP code for the user.
     */
    public function generateOtp()
    {
        $this->otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(10); // OTP expires in 10 minutes
        $this->save();
        
        return $this->otp_code;
    }

    /**
     * Check if the provided OTP is valid.
     */
    public function isValidOtp($otp)
    {
        return $this->otp_code === $otp && 
               $this->otp_expires_at && 
               $this->otp_expires_at->isFuture();
    }

    /**
     * Clear the OTP code.
     */
    public function clearOtp()
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }
}
