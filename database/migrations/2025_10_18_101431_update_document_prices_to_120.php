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
        // Update all document prices to 120.00
        DB::table('documents')->update(['price' => 120.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert prices back to 0.00 (original state)
        DB::table('documents')->update(['price' => 0.00]);
    }
};
