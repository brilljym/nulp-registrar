<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('onsite_requests', 'reason')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->text('reason')->nullable()->after('document_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('onsite_requests', 'reason')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
    }
};
