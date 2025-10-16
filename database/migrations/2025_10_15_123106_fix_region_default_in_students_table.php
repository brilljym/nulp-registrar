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
        // Set default value for existing region column
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE students ALTER COLUMN region SET DEFAULT 'Unset'");
        // Update any NULL values to have the default
        \Illuminate\Support\Facades\DB::statement("UPDATE students SET region = 'Unset' WHERE region IS NULL OR region = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the default value
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE students ALTER COLUMN region DROP DEFAULT");
    }
};
