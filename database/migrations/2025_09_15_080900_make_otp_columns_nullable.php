<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY otp_code VARCHAR(6) NULL");
        DB::statement("ALTER TABLE users MODIFY otp_expires_at TIMESTAMP NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY otp_code VARCHAR(6) NOT NULL DEFAULT '000000'");
        DB::statement("ALTER TABLE users MODIFY otp_expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }
};
