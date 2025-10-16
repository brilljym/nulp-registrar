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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onsite_request_id');
            $table->integer('rating')->comment('Rating from 1-5');
            $table->text('comment')->nullable()->comment('Optional feedback comment');
            $table->string('full_name')->comment('Name of the person giving feedback');
            $table->timestamps();
            
            $table->foreign('onsite_request_id')->references('id')->on('onsite_requests')->onDelete('cascade');
            $table->index('onsite_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
