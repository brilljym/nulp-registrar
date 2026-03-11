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
        Schema::create('onsite_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onsite_request_id');
            $table->unsignedBigInteger('document_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->foreign('onsite_request_id')->references('id')->on('onsite_requests')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onsite_request_items');
    }
};
