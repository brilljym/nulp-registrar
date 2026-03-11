<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update student_requests status enum to include 'ready_for_pickup'
        Schema::table('student_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'ready_for_release', 'ready_for_pickup', 'completed'])->default('pending')->change();
        });

        // Update onsite_requests status enum to include 'ready_for_pickup'
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'released', 'ready_for_pickup', 'completed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert student_requests status enum to original values
        Schema::table('student_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'ready_for_release', 'completed'])->default('pending')->change();
        });

        // Revert onsite_requests status enum to original values  
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'released', 'completed'])->default('pending')->change();
        });
    }
};
