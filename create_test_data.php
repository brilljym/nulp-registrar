<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use App\Models\Student;
use App\Models\User;

try {
    // Create test user and student if they don't exist
    $user = User::firstOrCreate(['school_email' => 'test@example.com'], [
        'first_name' => 'Test',
        'last_name' => 'Student',
        'school_email' => 'test@example.com',
        'personal_email' => 'test.personal@example.com',
        'password' => bcrypt('password'),
        'role_id' => 1
    ]);

    $student = Student::firstOrCreate(['user_id' => $user->id], [
        'user_id' => $user->id,
        'student_id' => 'TEST001',
        'course' => 'Computer Science',
        'year_level' => '4th Year',
        'department' => 'College of Computing and Information Sciences',
        'mobile_number' => '09123456789'
    ]);

    // Clear existing test data
    StudentRequest::where('queue_number', 'LIKE', 'TEST%')->delete();
    OnsiteRequest::where('queue_number', 'LIKE', 'TEST%')->delete();

    // Create test student requests
    StudentRequest::create([
        'queue_number' => 'TEST001',
        'student_id' => $student->id,
        'current_step' => 'registrar',
        'status' => 'in_queue',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    StudentRequest::create([
        'queue_number' => 'TEST002',
        'student_id' => $student->id,
        'current_step' => 'registrar',
        'status' => 'ready_for_pickup',
        'created_at' => now()->addMinutes(5),
        'updated_at' => now()->addMinutes(5)
    ]);

    StudentRequest::create([
        'queue_number' => 'TEST003',
        'student_id' => $student->id,
        'current_step' => 'registrar',
        'status' => 'waiting',
        'created_at' => now()->addMinutes(10),
        'updated_at' => now()->addMinutes(10)
    ]);

    // Create test onsite requests
    OnsiteRequest::create([
        'queue_number' => 'TEST004',
        'student_id' => $student->id,
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'mobile_number' => '09123456789',
        'current_step' => 'registrar',
        'status' => 'in_queue',
        'created_at' => now()->addMinutes(15),
        'updated_at' => now()->addMinutes(15)
    ]);

    OnsiteRequest::create([
        'queue_number' => 'TEST005',
        'student_id' => $student->id,
        'full_name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'mobile_number' => '09987654321',
        'current_step' => 'registrar',
        'status' => 'ready_for_pickup',
        'created_at' => now()->addMinutes(20),
        'updated_at' => now()->addMinutes(20)
    ]);

    OnsiteRequest::create([
        'queue_number' => 'TEST006',
        'student_id' => $student->id,
        'full_name' => 'Mike Wilson',
        'email' => 'mike@example.com',
        'mobile_number' => '09111111111',
        'current_step' => 'registrar',
        'status' => 'waiting',
        'created_at' => now()->addMinutes(25),
        'updated_at' => now()->addMinutes(25)
    ]);

    echo "✅ Test data created successfully!\n";
    echo "📊 Queue Status:\n";
    echo "   In Queue: " . collect()
        ->merge(StudentRequest::where('status', 'in_queue')->get())
        ->merge(OnsiteRequest::where('status', 'in_queue')->get())
        ->count() . " requests\n";
    echo "   Ready for Pickup: " . collect()
        ->merge(StudentRequest::where('status', 'ready_for_pickup')->get())
        ->merge(OnsiteRequest::where('status', 'ready_for_pickup')->get())
        ->count() . " requests\n";
    echo "   Waiting: " . collect()
        ->merge(StudentRequest::where('status', 'waiting')->get())
        ->merge(OnsiteRequest::where('status', 'waiting')->get())
        ->count() . " requests\n";
    
    echo "\n🌐 You can now test the queue display at:\n";
    echo "   Public: http://127.0.0.1:8000/queue-display\n";
    echo "   Registrar: http://127.0.0.1:8000/registrar/queue/display (requires login)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}