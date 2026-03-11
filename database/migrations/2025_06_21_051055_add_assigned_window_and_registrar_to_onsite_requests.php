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
            $table->unsignedBigInteger('assigned_registrar_id')->nullable()->after('id');

            $table->foreign('assigned_registrar_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_registrar_id']);
            $table->dropColumn(['assigned_registrar_id']);
        });
    }


};
