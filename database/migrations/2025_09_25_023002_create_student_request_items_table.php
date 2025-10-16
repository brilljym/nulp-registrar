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
        Schema::create('student_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_request_id');
            $table->unsignedBigInteger('document_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('student_request_id')->references('id')->on('student_requests')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_request_items');
    }
};
