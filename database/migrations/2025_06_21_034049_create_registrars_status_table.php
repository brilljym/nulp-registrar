<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrarsStatusTable extends Migration
{
    public function up(): void
    {
        Schema::create('registrars_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Registrar's user ID
            $table->unsignedTinyInteger('window_number')->nullable(); // 1–6
            $table->boolean('is_available')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrars_status');
    }
}
