<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update document prices to new values
        DB::table('documents')->where('type_document', 'Transcript of Records with Documentary Stamp')->update(['price' => 1030.00]);
        DB::table('documents')->where('type_document', 'Certificates (Any) with Documentary Stamp')->update(['price' => 157.00]);
        DB::table('documents')->where('type_document', 'Certificates (Any) without Documentary Stamp')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'Form 137')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'CTC of Grades Per Term')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'CTC of Diploma (Per set)')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'CTC of TOR (Per set)')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'Copy of Diploma with Documentary Stamp')->update(['price' => 1453.00]);
        DB::table('documents')->where('type_document', 'Honorable Dismissal (HD/Transfer Credentials w/ Doc. Stamp)')->update(['price' => 1030.00]);
        DB::table('documents')->where('type_document', 'Reprinting of COR-Stamp Enrolled/CTC/Copy of Grades')->update(['price' => 54.00]);
        DB::table('documents')->where('type_document', 'Certificates of Good Moral')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'Course Descriptions')->update(['price' => 123.00]);
        DB::table('documents')->where('type_document', 'Documentary Stamp')->update(['price' => 30.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous prices (120.00 for all)
        DB::table('documents')->update(['price' => 120.00]);
    }
};
