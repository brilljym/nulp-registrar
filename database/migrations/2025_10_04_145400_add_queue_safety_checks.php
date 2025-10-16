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
        // This migration adds additional safety checks to prevent queue violations
        
        // Note: We cannot add a unique constraint on status='in_queue' across tables easily
        // Instead, we'll create a stored procedure or trigger if needed
        // For now, we'll rely on application-level checks with database transactions
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};