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
        Schema::table('student_requests', function (Blueprint $table) {
            $table->string('payment_receipt_path')->nullable()->after('total_cost');
            $table->boolean('payment_approved')->default(false)->after('payment_receipt_path');
            $table->unsignedBigInteger('approved_by_accounting_id')->nullable()->after('payment_approved');
            $table->timestamp('payment_approved_at')->nullable()->after('approved_by_accounting_id');
            
            $table->foreign('approved_by_accounting_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by_accounting_id']);
            $table->dropColumn(['payment_receipt_path', 'payment_approved', 'approved_by_accounting_id', 'payment_approved_at']);
        });
    }
};
