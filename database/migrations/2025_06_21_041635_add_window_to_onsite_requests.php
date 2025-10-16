<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('onsite_requests', 'window_id')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('window_id')->nullable()->after('id');
            });
        }

        $fk = DB::selectOne("
            SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'onsite_requests'
              AND COLUMN_NAME = 'window_id'
              AND REFERENCED_TABLE_NAME = 'windows'
            LIMIT 1
        ");
        if (!$fk) {
            DB::statement("
                ALTER TABLE onsite_requests
                ADD CONSTRAINT onsite_requests_window_id_foreign
                FOREIGN KEY (window_id) REFERENCES windows(id)
                ON DELETE SET NULL ON UPDATE CASCADE
            ");
        }
    }

    public function down(): void
    {
        $row = DB::selectOne("
            SELECT CONSTRAINT_NAME AS name
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'onsite_requests'
              AND COLUMN_NAME = 'window_id'
              AND REFERENCED_TABLE_NAME = 'windows'
            LIMIT 1
        ");
        if ($row?->name) {
            DB::statement("ALTER TABLE onsite_requests DROP FOREIGN KEY {$row->name}");
        }
        if (Schema::hasColumn('onsite_requests', 'window_id')) {
            Schema::table('onsite_requests', function (Blueprint $table) {
                $table->dropColumn('window_id');
            });
        }
    }
};
