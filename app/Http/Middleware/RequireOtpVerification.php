<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class RequireOtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip middleware for non-authenticated users
        if (!$user) {
            return $next($request);
        }
        
        // Skip middleware for users without supported roles or if 2FA is not enabled
        $supportedRoles = ['student', 'registrar'];
        if (!$user->role || !in_array($user->role->name, $supportedRoles) || !$user->two_factor_enabled) {
            \Log::info('OTP Middleware: Skipping verification', [
                'user_id' => $user->id,
                'role' => $user->role ? $user->role->name : 'no_role',
                'two_factor_enabled' => $user->two_factor_enabled,
                'reason' => 'Unsupported role or 2FA disabled'
            ]);
            return $next($request);
        }
        
        // Skip middleware for 2FA-related routes
        $exemptRoutes = [
            'student.2fa.*',
            'registrar.2fa.*',
            'logout.*',
        ];
        
        foreach ($exemptRoutes as $exemptRoute) {
            if ($request->routeIs($exemptRoute)) {
                return $next($request);
            }
        }
        
        // Check if OTP has been verified in this session
        $otpVerified = session('otp_verified', false);
        $otpVerifiedAt = session('otp_verified_at');
        
        // Require re-verification if more than 4 hours have passed
        if ($otpVerified && $otpVerifiedAt) {
            $verifiedAt = Carbon::parse($otpVerifiedAt);
            if ($verifiedAt->diffInHours(now()) > 4) {
                session()->forget(['otp_verified', 'otp_verified_at']);
                $otpVerified = false;
            }
        }
        
        // Redirect to OTP verification if not verified
        if (!$otpVerified) {
            \Log::info('OTP Middleware: Redirecting to verification', [
                'user_id' => $user->id,
                'role' => $user->role->name,
                'otp_verified' => $otpVerified,
                'otp_verified_at' => $otpVerifiedAt
            ]);
            
            // Redirect to appropriate 2FA verification based on role
            if ($user->role->name === 'student') {
                return redirect()->route('student.2fa.verify');
            } elseif ($user->role->name === 'registrar') {
                return redirect()->route('registrar.2fa.verify');
            }
        }
        
        \Log::info('OTP Middleware: Allowing through', [
            'user_id' => $user->id,
            'otp_verified' => $otpVerified
        ]);
        
        return $next($request);
    }
}
