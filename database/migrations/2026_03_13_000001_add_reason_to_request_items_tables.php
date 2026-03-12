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
        if (! Schema::hasColumn('onsite_request_items', 'reason')) {
            Schema::table('onsite_request_items', function (Blueprint $table) {
                $table->text('reason')->nullable()->after('quantity');
            });
        }

        if (! Schema::hasColumn('student_request_items', 'reason')) {
            Schema::table('student_request_items', function (Blueprint $table) {
                $table->text('reason')->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('onsite_request_items', 'reason')) {
            Schema::table('onsite_request_items', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }

        if (Schema::hasColumn('student_request_items', 'reason')) {
            Schema::table('student_request_items', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
    }
};
