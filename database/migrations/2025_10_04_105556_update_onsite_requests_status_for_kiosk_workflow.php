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
        // Update onsite_requests status enum to include 'released', 'in_queue' and 'ready_for_pickup'
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'released', 'in_queue', 'ready_for_pickup', 'completed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert onsite_requests status enum to original values
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'registrar_approved', 'processing', 'released', 'ready_for_pickup', 'completed'])->default('pending')->change();
        });
    }
};
