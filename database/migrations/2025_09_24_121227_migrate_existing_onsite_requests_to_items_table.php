<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\OnsiteRequestItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing onsite requests to the new items structure
        $requests = DB::table('onsite_requests')
            ->whereNotNull('document_id')
            ->whereNotNull('quantity')
            ->get();

        foreach ($requests as $request) {
            DB::table('onsite_request_items')->insert([
                'onsite_request_id' => $request->id,
                'document_id' => $request->document_id,
                'quantity' => $request->quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all onsite request items
        DB::table('onsite_request_items')->delete();
    }
};
