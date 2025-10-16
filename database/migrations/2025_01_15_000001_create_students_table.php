<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->string('course');
            $table->string('year_level');
            $table->string('department');
            $table->string('mobile_number');
            $table->string('house_number')->nullable();
            $table->string('block_number')->nullable();
            $table->string('street');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
