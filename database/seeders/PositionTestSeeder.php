<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentRequest;
use App\Models\Student;
use App\Models\User;
use App\Models\Document;
use App\Models\StudentRequestItem;
use Carbon\Carbon;

class PositionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing test data
        StudentRequest::where('reference_no', 'like', 'TEST-%')->delete();

        // Create test students and users if they don't exist
        if (Student::count() < 11) {
            $studentRoleId = 1; // Use admin role for simplicity

            for ($i = 1; $i <= 11; $i++) {
                $user = User::create([
                    'first_name' => 'TestStudent' . $i,
                    'last_name' => 'User' . $i,
                    'school_email' => 'teststudent' . $i . '@student.nu-lipa.edu.ph',
                    'personal_email' => 'teststudent' . $i . '@example.com',
                    'password' => bcrypt('password'),
                    'role_id' => $studentRoleId,
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'student_number' => '2024' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'course' => 'BS Computer Science',
                    'year_level' => '3rd Year',
                ]);
            }
        }

        // Get students, documents, and users
        $students = Student::take(11)->get();
        $documents = Document::all();

        if ($students->isEmpty() || $documents->isEmpty()) {
            $this->command->error('Failed to create or find required data.');
            return;
        }

        // Create 5 WAITING requests
        for ($i = 1; $i <= 5; $i++) {
            $student = $students->get($i - 1);

            $request = StudentRequest::create([
                'student_id' => $student->id,
                'reference_no' => 'TEST-WAIT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'waiting',
                'reason' => 'Test waiting request ' . $i,
                'remarks' => 'Position test - waiting',
                'total_cost' => 50.00,
                'payment_confirmed' => true,
                'payment_approved' => true,
                'approved_by_accounting_id' => 1,
                'payment_approved_at' => Carbon::now()->subMinutes(rand(10, 60)),
                'queue_number' => 'Q' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now()->subMinutes(60 + ($i * 5)), // Older requests first
                'updated_at' => Carbon::now()->subMinutes(60 + ($i * 5)),
            ]);

            // Add request items
            StudentRequestItem::create([
                'student_request_id' => $request->id,
                'document_id' => $documents->first()->id,
                'quantity' => 1,
                'cost' => 50.00,
            ]);
        }

        // Create 3 IN_QUEUE requests
        for ($i = 1; $i <= 3; $i++) {
            $student = $students->get(5 + $i - 1);

            $request = StudentRequest::create([
                'student_id' => $student->id,
                'reference_no' => 'TEST-QUEUE-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'in_queue',
                'reason' => 'Test in queue request ' . $i,
                'remarks' => 'Position test - in queue',
                'total_cost' => 50.00,
                'payment_confirmed' => true,
                'payment_approved' => true,
                'approved_by_accounting_id' => 1,
                'payment_approved_at' => Carbon::now()->subMinutes(rand(10, 60)),
                'queue_number' => 'Q' . str_pad(5 + $i, 3, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now()->subMinutes(30 + ($i * 2)),
                'updated_at' => Carbon::now()->subMinutes(30 + ($i * 2)),
            ]);

            // Add request items
            StudentRequestItem::create([
                'student_request_id' => $request->id,
                'document_id' => $documents->first()->id,
                'quantity' => 1,
                'cost' => 50.00,
            ]);
        }

        // Create 3 READY_FOR_PICKUP requests
        for ($i = 1; $i <= 3; $i++) {
            $student = $students->get(8 + $i - 1);

            $request = StudentRequest::create([
                'student_id' => $student->id,
                'reference_no' => 'TEST-PICKUP-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'ready_for_pickup',
                'reason' => 'Test ready for pickup request ' . $i,
                'remarks' => 'Position test - ready for pickup',
                'total_cost' => 50.00,
                'payment_confirmed' => true,
                'payment_approved' => true,
                'approved_by_accounting_id' => 1,
                'payment_approved_at' => Carbon::now()->subMinutes(rand(10, 60)),
                'queue_number' => 'Q' . str_pad(8 + $i, 3, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now()->subMinutes(15 + ($i * 1)),
                'updated_at' => Carbon::now()->subMinutes(15 + ($i * 1)),
            ]);

            // Add request items
            StudentRequestItem::create([
                'student_request_id' => $request->id,
                'document_id' => $documents->first()->id,
                'quantity' => 1,
                'cost' => 50.00,
            ]);
        }

        $this->command->info('Position test data created:');
        $this->command->info('5 WAITING requests (TEST-WAIT-001 to TEST-WAIT-005)');
        $this->command->info('3 IN_QUEUE requests (TEST-QUEUE-001 to TEST-QUEUE-003)');
        $this->command->info('3 READY_FOR_PICKUP requests (TEST-PICKUP-001 to TEST-PICKUP-003)');
        $this->command->info('');
        $this->command->info('Test queue numbers: Q001-Q011');
        $this->command->info('Waiting requests should show positions 1-5');
        $this->command->info('In-queue and ready-for-pickup should NOT show position numbers');
    }
}