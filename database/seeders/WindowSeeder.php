<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WindowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        foreach (range(1, 3) as $i) {
            \App\Models\Window::create([
                'name' => "Window $i",
                'window_number' => $i,
                'registrar_id' => null, // Will be set when registrars are assigned
            ]);
        }
    }

}
