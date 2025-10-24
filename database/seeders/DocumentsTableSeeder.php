<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // Remove existing entries so the seeder contains only the requested list
        DB::table('documents')->delete();

        DB::table('documents')->insert([
            ['type_document' => 'Transcript of Records with Documentary Stamp', 'price' => 1030.00],
            ['type_document' => 'Certificates (Any) with Documentary Stamp', 'price' => 157.00],
            ['type_document' => 'Certificates (Any) without Documentary Stamp', 'price' => 123.00],
            ['type_document' => 'Form 137', 'price' => 123.00],
            ['type_document' => 'CTC of Grades Per Term', 'price' => 123.00],
            ['type_document' => 'CTC of Diploma (Per set)', 'price' => 123.00],
            ['type_document' => 'CTC of TOR (Per set)', 'price' => 123.00],
            ['type_document' => 'Copy of Diploma with Documentary Stamp', 'price' => 1453.00],
            ['type_document' => 'Honorable Dismissal (HD/Transfer Credentials w/ Doc. Stamp)', 'price' => 1030.00],
            ['type_document' => 'Reprinting of COR-Stamp Enrolled/CTC/Copy of Grades', 'price' => 54.00],
            ['type_document' => 'Certificates of Good Moral', 'price' => 123.00],
            ['type_document' => 'Course Descriptions', 'price' => 123.00],
            ['type_document' => 'Documentary Stamp', 'price' => 30.00],
        ]);
    }

}
