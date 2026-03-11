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
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('request_type'); // 'student' or 'onsite'
            $table->unsignedBigInteger('request_id');
            $table->string('queue_number');
            $table->string('customer_name');
            $table->json('documents'); // Array of documents
            $table->decimal('total_cost', 8, 2)->nullable();
            $table->text('qr_data'); // QR code data/URL
            $table->enum('status', ['pending', 'printing', 'completed', 'failed'])->default('pending');
            $table->timestamp('printed_at')->nullable();
            $table->string('printer_name')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('queue_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
