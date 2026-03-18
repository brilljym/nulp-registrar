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
        // Add qr_code_path to onsite_requests
        if (!Schema::hasColumn('onsite_requests', 'qr_code_path')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->string('qr_code_path')->nullable()->after('payment_receipt_path');
            });
        }

        // Add qr_code_path to student_requests
        if (!Schema::hasColumn('student_requests', 'qr_code_path')) {
            Schema::table('student_requests', function (Blueprint $table) {
                $table->string('qr_code_path')->nullable()->after('payment_receipt_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            if (Schema::hasColumn('onsite_requests', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });

        Schema::table('student_requests', function (Blueprint $table) {
            if (Schema::hasColumn('student_requests', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });
    }
};
