<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed roles using Eloquent (if Role model exists)
        Role::insert([
            ['id' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'registrar', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'accounting', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
