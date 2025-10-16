<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('onsite_requests', 'window_number')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->dropColumn('window_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('onsite_requests', 'window_number')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->integer('window_number')->nullable();
            });
        }
    }
};
