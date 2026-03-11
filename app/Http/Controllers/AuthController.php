<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** ---------------- Original: Login form ---------------- */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function showLoginFormPage()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login-form');
    }

    /** ---------------- Login: authenticate users directly ---------------- */
    public function login(Request $request)
    {
        $request->validate([
            'school_email' => 'required|email',
            'password'     => 'required|string',
        ]);

        $credentials = $request->only('school_email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Debug logging
            \Log::info('User login attempt', [
                'user_id' => $user->id,
                'email' => $user->school_email,
                'role_name' => $user->role ? $user->role->name : 'no_role',
                'two_factor_enabled' => $user->two_factor_enabled ?? false
            ]);

            // Check if user has 2FA enabled
            if ($user->role && $user->two_factor_enabled) {
                // Clear any existing OTP verification status
                session()->forget(['otp_verified', 'otp_verified_at']);

                \Log::info('Redirecting to 2FA verification', [
                    'user_id' => $user->id,
                    'role' => $user->role->name,
                    'reason' => 'User with 2FA enabled'
                ]);

                // Redirect to appropriate 2FA verification based on role
                if ($user->role->name === 'student') {
                    return redirect()->route('student.2fa.verify');
                } elseif ($user->role->name === 'registrar') {
                    return redirect()->route('registrar.2fa.verify');
                }
            }

            \Log::info('Login successful, redirecting to dashboard', [
                'user_id' => $user->id,
                'is_student' => $user->role && $user->role->name === 'student',
                'two_factor_enabled' => $user->two_factor_enabled,
                'should_2fa' => false
            ]);

            return redirect('/dashboard');
        }

        return back()->withErrors(['school_email' => 'Invalid credentials.']);
    }

    /** ---------------- Original: Register form ---------------- */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.register');
    }

    /** ---------------- Original: Register (unchanged) ---------------- */
    public function register(Request $request)
    {
        $request->validate([
            'first_name'     => 'required',
            'middle_name'    => 'nullable',
            'last_name'      => 'required',
            'personal_email' => 'required|email|unique:users,personal_email|unique:users,school_email',
            'password'       => [
                'required', 'string', 'min:6', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one lowercase, one uppercase letter, and one number.',
        ]);

        // Email Generator
        $school_email = $this->generateSchoolEmail(
            $request->first_name,
            $request->middle_name,
            $request->last_name
        );

        try {
            // Create User
            $user = User::create([
                'first_name'     => $request->first_name,
                'middle_name'    => $request->middle_name,
                'last_name'      => $request->last_name,
                'school_email'   => $school_email,
                'personal_email' => $request->personal_email,
                'password'       => Hash::make($request->password),
                'role_id'        => 3, // Student
            ]);

            // Create Student
            Student::create([
                'user_id'       => $user->id,
                'student_id'    => Student::generateStudentId(),
                'course'        => '',
                'year_level'    => '',
                'department'    => '',
                'mobile_number' => '',
                'house_number'  => null,
                'block_number'  => null,
                'street'        => '',
                'barangay'      => '',
                'city'          => '',
                'province'      => '',
            ]);

            Auth::login($user);
            return redirect('/dashboard');
        } catch (\Throwable $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withErrors(['registration' => 'Something went wrong. Please try again.']);
        }
    }

    /** ---------------- Logout (safer: invalidate session + rotate CSRF) ---------------- */
    public function logout(Request $request)
    {
        // Clear 2FA session data before logout
        $request->session()->forget(['otp_verified', 'otp_verified_at']);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /** ---------------- Original: Email generator ---------------- */
    private function generateSchoolEmail($firstName, $middleName, $lastName): string
    {
        $fi = strtolower(substr($firstName, 0, 1));
        $mi = $middleName ? strtolower(substr($middleName, 0, 1)) : '';
        $ln = strtolower($lastName);

        return $mi
            ? "{$ln}{$fi}{$mi}@student.nu-lipa.edu.ph"
            : "{$ln}{$fi}@student.nu-lipa.edu.ph";
    }

    /** ---------------- Forgot Password: Show form ---------------- */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.forgot-password');
    }

    /** ---------------- Forgot Password: Send reset link ---------------- */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,personal_email',
        ]);

        // Generate reset token
        $token = Str::random(64);
        $email = $request->email;

        // Store token in database
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token, // Store plain token, not hashed
                'created_at' => now()
            ]
        );

        // Send email with reset link
        try {
            \Mail::to($email)->send(new \App\Mail\ResetPasswordMail($token, $email));
            return back()->with('status', 'Password reset link has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset email. Please try again.']);
        }
    }

    /** ---------------- Reset Password: Show form ---------------- */
    public function showResetPasswordForm(Request $request, $token)
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.reset-password', [
            'token' => $token,
            'email' => urldecode($request->query('email'))
        ]);
    }

    /** ---------------- Reset Password: Update password ---------------- */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,personal_email',
            'password' => [
                'required', 'string', 'min:6', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one lowercase, one uppercase letter, and one number.',
        ]);

        // Verify token
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || $request->token !== $resetRecord->token) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Check if token is not expired (24 hours)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            return back()->withErrors(['token' => 'Reset token has expired. Please request a new one.']);
        }

        // Update password
        $user = User::where('personal_email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Log the user in
        Auth::login($user);

        return redirect('/dashboard')->with('status', 'Password has been reset successfully.');
    }

}
