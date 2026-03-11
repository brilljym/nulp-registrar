<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('onsite_requests', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('reason');
            }
            if (!Schema::hasColumn('onsite_requests', 'department')) {
                $table->string('department')->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            if (Schema::hasColumn('onsite_requests', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('onsite_requests', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
