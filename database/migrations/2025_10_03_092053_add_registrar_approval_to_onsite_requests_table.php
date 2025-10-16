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
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->boolean('registrar_approved')->default(false)->after('expected_release_date');
            $table->unsignedBigInteger('approved_by_registrar_id')->nullable()->after('registrar_approved');
            $table->timestamp('registrar_approved_at')->nullable()->after('approved_by_registrar_id');
            $table->foreign('approved_by_registrar_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by_registrar_id']);
            $table->dropColumn(['registrar_approved', 'approved_by_registrar_id', 'registrar_approved_at']);
        });
    }
};
