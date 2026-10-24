<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            1 => 'Ikan Hias',
            2 => 'Pakan Ikan',
            3 => 'Aquarium & Aksesori',
        ] as $id => $name) {
            DB::table('categories')->updateOrInsert(
                ['category_id' => $id],
                ['category' => $name, 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
