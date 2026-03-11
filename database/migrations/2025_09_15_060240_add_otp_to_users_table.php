<?php

// database/migrations/2025_09_15_000001_fix_otp_columns.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // Check if columns exist and add them using Schema builder for better compatibility
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->default('000000')->after('password');
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });

        // Backfill null values
        DB::table('users')->whereNull('otp_code')->update(['otp_code' => '000000']);
        DB::table('users')->whereNull('otp_expires_at')->update(['otp_expires_at' => now()]);

        // Make columns NOT NULL if they exist
        if (Schema::hasColumn('users', 'otp_code')) {
            DB::statement("ALTER TABLE users MODIFY otp_code VARCHAR(6) NOT NULL DEFAULT '000000'");
        }
        if (Schema::hasColumn('users', 'otp_expires_at')) {
            DB::statement("ALTER TABLE users MODIFY otp_expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }
    }

    public function down(): void
    {
        // loosen back to nullable (keep the columns)
        DB::statement("ALTER TABLE users MODIFY otp_code VARCHAR(6) NULL");
        DB::statement("ALTER TABLE users MODIFY otp_expires_at TIMESTAMP NULL");
    }
};
