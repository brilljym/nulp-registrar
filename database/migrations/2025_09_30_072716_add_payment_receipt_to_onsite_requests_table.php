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
            $table->string('payment_receipt_path')->nullable();
            $table->boolean('payment_approved')->default(false);
            $table->unsignedBigInteger('approved_by_accounting_id')->nullable();
            $table->timestamp('payment_approved_at')->nullable();
            $table->foreign('approved_by_accounting_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onsite_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by_accounting_id']);
            $table->dropColumn(['payment_receipt_path', 'payment_approved', 'approved_by_accounting_id', 'payment_approved_at']);
        });
    }
};
