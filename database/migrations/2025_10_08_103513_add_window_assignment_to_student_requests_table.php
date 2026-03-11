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
        Schema::table('student_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('window_id')->nullable()->after('approved_by_registrar_id');
            $table->unsignedBigInteger('assigned_registrar_id')->nullable()->after('window_id');
            
            $table->foreign('window_id')->references('id')->on('windows')->onDelete('set null');
            $table->foreign('assigned_registrar_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_requests', function (Blueprint $table) {
            $table->dropForeign(['window_id']);
            $table->dropForeign(['assigned_registrar_id']);
            $table->dropColumn(['window_id', 'assigned_registrar_id']);
        });
    }
};
