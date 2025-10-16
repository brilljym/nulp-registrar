<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'David',
                'middle_name' => 'Simbahan',
                'last_name' => 'Malibiran',
                'password' => Hash::make('Admin123'),
                'school_email' => 'malibirands@student.nu-lipa.edu.ph',
                'personal_email' => 'david@gmail.com',
                'role_id' => 1, // admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Accounting',
                'middle_name' => '',
                'last_name' => 'Officer',
                'password' => Hash::make('Accounting123'),
                'school_email' => 'accounting@nu-lipa.edu.ph',
                'personal_email' => 'accounting@nu-lipa.edu.ph',
                'role_id' => 4, // accounting
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
