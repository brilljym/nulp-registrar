<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role:id,name')
                    ->select('id', 'first_name', 'middle_name', 'last_name', 'school_email', 'personal_email', 'role_id')
                    ->orderBy('id', 'desc')
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('role:id,name');
        
        // Load additional relationships based on role
        if ($user->role_id == 3) { // Student
            $user->load('student');
        } elseif ($user->role_id == 2) { // Registrar
            $user->load('registrar');
        }

        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'first_name'     => 'required',
            'middle_name'    => 'nullable',
            'last_name'      => 'required',
            'personal_email' => 'required|email|unique:users,personal_email',
            'role_id'        => 'required|exists:roles,id',
        ];

        // Add student-specific validation if role is student (role_id = 3)
        if ($request->role_id == 3) {
            $validationRules = array_merge($validationRules, [
                'course'      => 'required|string|min:1',
                'year_level'  => 'required|string|min:1',
                'department'  => 'required|string|min:1',
            ]);
        }

        try {
            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $school_email = $this->generateSchoolEmail(
            $request->first_name,
            $request->middle_name,
            $request->last_name
        );

        // Generate random password
        $password = $this->generateRandomPassword();

        $user = User::create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'personal_email' => $request->personal_email,
            'school_email'   => $school_email,
            'password'       => Hash::make($password),
            'role_id'        => $request->role_id,
        ]);

        try {
            if ($user->role_id == 3) {
                Student::create([
                    'user_id'       => $user->id,
                    'student_id'    => Student::generateStudentId(),
                    'course'        => $request->course ?? 'Unset',
                    'department'    => $request->department ?? 'Unset',
                    'year_level'    => $request->year_level ?? 'Unset',
                    'mobile_number' => 'Unset',
                    'house_number'  => null,
                    'block_number'  => null,
                    'street'        => 'Unset',
                    'barangay'      => 'Unset',
                    'city'          => 'Unset',
                    'province'      => 'Unset',
                ]);
            } elseif ($user->role_id == 2) {
                // Find the next available window number (1-6)
                $usedWindows = Registrar::whereNotNull('window_number')
                    ->where('window_number', '!=', 'Unset')
                    ->pluck('window_number')
                    ->toArray();
                
                // Find the lowest available window number from 1-6
                $nextWindow = null;
                for ($i = 1; $i <= 6; $i++) {
                    if (!in_array($i, $usedWindows)) {
                        $nextWindow = $i;
                        break;
                    }
                }
                
                // If all windows are taken, still assign but log a warning
                if ($nextWindow === null) {
                    Log::warning("All window numbers (1-6) are already assigned. Assigning window 1 to new registrar.");
                    $nextWindow = 1;
                }

                Registrar::create([
                    'user_id' => $user->id,
                    'window_number' => $nextWindow,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Auto-creation failed for user ID {$user->id}: " . $e->getMessage());
        }

        // Send credentials email to personal email
        try {
            Mail::to($user->personal_email)->send(new UserCredentialsMail($user, $password));
        } catch (\Throwable $e) {
            Log::error("Failed to send credentials email to {$user->personal_email}: " . $e->getMessage());
        }

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'type' => 'create',
                'user' => $user->load('role')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validationRules = [
            'first_name'     => 'required',
            'middle_name'    => 'nullable',
            'last_name'      => 'required',
            'personal_email' => 'required|email|unique:users,personal_email,' . $user->id,
            'password'       => [
                'nullable', 'min:6', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
            ],
            'role_id'        => 'required|exists:roles,id',
        ];

        // Add student-specific validation if role is student (role_id = 3)
        if ($request->role_id == 3) {
            $validationRules = array_merge($validationRules, [
                'course'      => 'required|string|min:1',
                'year_level'  => 'required|string|min:1',
                'department'  => 'required|string|min:1',
            ]);
        }

        try {
            $request->validate($validationRules, [
                'password.regex' => 'Password must contain at least one lowercase, one uppercase letter, and one number.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $school_email = $this->generateSchoolEmail(
            $request->first_name,
            $request->middle_name,
            $request->last_name
        );

        $data = [
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'personal_email' => $request->personal_email,
            'school_email'   => $school_email,
            'role_id'        => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update or create student record if role is student
        if ($request->role_id == 3) {
            $studentData = [
                'course'     => $request->course,
                'year_level' => $request->year_level,
                'department' => $request->department,
            ];

            $student = $user->student;
            if ($student) {
                // Update existing student record
                $student->update($studentData);
            } else {
                // Create new student record if it doesn't exist
                Student::create(array_merge($studentData, [
                    'user_id'       => $user->id,
                    'student_id'    => Student::generateStudentId(),
                    'mobile_number' => 'Unset',
                    'region'        => 'Unset',
                    'house_number'  => null,
                    'block_number'  => null,
                    'street'        => 'Unset',
                    'barangay'      => 'Unset',
                    'city'          => 'Unset',
                    'province'      => 'Unset',
                ]));
            }
        } elseif ($user->student) {
            // If role changed from student to something else, we might want to keep the student record
            // but not delete it as it might have historical data
            // Optionally, you could delete it: $user->student->delete();
        }

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'type' => 'update',
                'user' => $user->load('role')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userName = $user->first_name . ' ' . $user->last_name;
        $user->delete();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
                'type' => 'delete',
                'user_name' => $userName
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    private function generateSchoolEmail($firstName, $middleName, $lastName)
    {
        $fi = strtolower(substr($firstName, 0, 1));
        $mi = $middleName ? strtolower(substr($middleName, 0, 1)) : '';
        $ln = strtolower($lastName);

        return $mi
            ? "{$ln}{$fi}{$mi}@student.nu-lipa.edu.ph"
            : "{$ln}{$fi}@student.nu-lipa.edu.ph";
    }

    private function generateRandomPassword()
    {
        $length = 8;
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';

        $password = '';

        // Ensure exactly one uppercase letter
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        
        // Ensure exactly one number
        $password .= $numbers[rand(0, strlen($numbers) - 1)];

        // Fill the rest with lowercase letters (6 more characters for total of 8)
        for ($i = 2; $i < $length; $i++) {
            $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        }

        // Shuffle the password to randomize positions
        return str_shuffle($password);
    }
}
