<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnsiteRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('onsite_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable(); // link if matched
            $table->string('full_name');
            $table->string('course');
            $table->string('year_level');
            $table->string('department');
            $table->string('ref_code')->nullable(); // provided during payment
            $table->enum('current_step', [
                'start', 'payment', 'window', 'processing', 'release', 'completed'
            ])->default('start');
            $table->unsignedTinyInteger('window_number')->nullable(); // ex. 1–6
            $table->enum('status', ['pending', 'processing', 'released', 'completed'])->default('pending');

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onsite_requests');
    }
}
