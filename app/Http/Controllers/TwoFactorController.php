<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use App\Models\User;

class TwoFactorController extends Controller
{
    /**
     * Toggle 2FA for the authenticated user.
     */
    public function toggle(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            
            \Log::info('2FA Toggle requested', [
                'user_id' => $user->id,
                'current_status' => $user->two_factor_enabled
            ]);
            
            $user->two_factor_enabled = !$user->two_factor_enabled;
            
            // If disabling 2FA, clear any existing OTP and session data
            if (!$user->two_factor_enabled) {
                $user->clearOtp();
                session()->forget(['otp_verified', 'otp_verified_at']);
            }
            
            $user->save();

            $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
            
            \Log::info('2FA Toggle completed', [
                'user_id' => $user->id,
                'new_status' => $user->two_factor_enabled,
                'session_cleared' => !$user->two_factor_enabled
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Two-factor authentication has been {$status}.",
                'two_factor_enabled' => $user->two_factor_enabled
            ]);
        } catch (\Exception $e) {
            \Log::error('2FA Toggle failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send OTP to user's email.
     */
    public function sendOtp(Request $request)
    {
        try {
            $user = Auth::user();
            
            \Log::info('SendOTP called', [
                'user_id' => $user ? $user->id : 'null',
                'email' => $user ? $user->school_email : 'null',
                'two_factor_enabled' => $user ? $user->two_factor_enabled : 'null'
            ]);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            
            if (!$user->two_factor_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication is not enabled.'
                ], 400);
            }

            $otp = $user->generateOtp();
            
            \Log::info('Generated OTP', ['user_id' => $user->id, 'otp_length' => strlen($otp)]);
            
            Mail::to($user->personal_email)->send(new OTPMail($otp, $user->first_name));
            
            \Log::info('OTP email sent successfully', ['user_id' => $user->id, 'email' => $user->personal_email]);
            
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            \Log::error('SendOTP failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $user = Auth::user();
        
        if (!$user->isValidOtp($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP code.'
            ], 400);
        }

        // Clear the OTP and mark as verified in session
        $user->clearOtp();
        session(['otp_verified' => true, 'otp_verified_at' => now()]);

        // Determine redirect URL based on user role
        $redirectUrl = '/dashboard'; // Default fallback
        if ($user->role) {
            switch ($user->role->name) {
                case 'student':
                    $redirectUrl = route('student.dashboard');
                    break;
                case 'registrar':
                    $redirectUrl = route('registrar.dashboard');
                    break;
                default:
                    $redirectUrl = '/dashboard';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Show OTP verification form.
     */
    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    /**
     * Get 2FA status for the authenticated user.
     */
    public function getStatus()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            
            return response()->json([
                'success' => true,
                'two_factor_enabled' => $user->two_factor_enabled ?? false,
                'user_role' => $user->role ? $user->role->name : null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting 2FA status', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get 2FA status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
