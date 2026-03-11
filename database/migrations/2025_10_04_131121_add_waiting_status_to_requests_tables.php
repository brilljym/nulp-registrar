<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'waiting' status to student_requests status enum
        DB::statement("ALTER TABLE student_requests MODIFY COLUMN status ENUM('pending', 'registrar_approved', 'denied', 'processing', 'released', 'ready_for_release', 'ready_for_pickup', 'completed', 'cancelled', 'in_queue', 'waiting') DEFAULT 'pending'");
        
        // Add 'waiting' status to onsite_requests status enum
        DB::statement("ALTER TABLE onsite_requests MODIFY COLUMN status ENUM('pending', 'registrar_approved', 'denied', 'processing', 'released', 'ready_for_release', 'ready_for_pickup', 'completed', 'cancelled', 'in_queue', 'waiting') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'waiting' status from student_requests status enum
        DB::statement("ALTER TABLE student_requests MODIFY COLUMN status ENUM('pending', 'registrar_approved', 'denied', 'processing', 'released', 'ready_for_release', 'ready_for_pickup', 'completed', 'cancelled', 'in_queue') DEFAULT 'pending'");
        
        // Remove 'waiting' status from onsite_requests status enum  
        DB::statement("ALTER TABLE onsite_requests MODIFY COLUMN status ENUM('pending', 'registrar_approved', 'denied', 'processing', 'released', 'ready_for_release', 'ready_for_pickup', 'completed', 'cancelled', 'in_queue') DEFAULT 'pending'");
    }
};
