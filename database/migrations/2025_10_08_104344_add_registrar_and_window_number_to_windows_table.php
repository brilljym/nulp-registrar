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
        Schema::table('windows', function (Blueprint $table) {
            $table->unsignedBigInteger('registrar_id')->nullable()->after('name');
            $table->integer('window_number')->nullable()->after('registrar_id');
            
            $table->foreign('registrar_id')->references('id')->on('registrars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('windows', function (Blueprint $table) {
            $table->dropForeign(['registrar_id']);
            $table->dropColumn(['registrar_id', 'window_number']);
        });
    }
};
