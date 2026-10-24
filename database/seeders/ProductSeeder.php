<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Ikan Cupang Halfmoon Red', 'description' => 'Ikan cupang berwarna merah dengan sirip halfmoon.', 'category_id' => 1, 'price' => 75000, 'img' => null, 'stock' => 5, 'slug' => 'ikan-cupang-halfmoon-red'],
            ['name' => 'Ikan Arwana Super Red 40–45cm', 'description' => 'Ikan arwana premium untuk aquarium besar.', 'category_id' => 1, 'price' => 3500000, 'img' => null, 'stock' => 2, 'slug' => 'ikan-arwana-super-red'],
            ['name' => 'Ikan Mas Koki Oranda', 'description' => 'Ikan mas koki Oranda merah putih.', 'category_id' => 1, 'price' => 120000, 'img' => null, 'stock' => 10, 'slug' => 'ikan-mas-koki-oranda'],
            ['name' => 'Ikan Guppy Dragon Big Ear', 'description' => 'Ikan guppy dengan warna cerah dan sirip lebar.', 'category_id' => 1, 'price' => 45000, 'img' => null, 'stock' => 15, 'slug' => 'ikan-guppy-dragon-big-ear'],
            ['name' => 'Ikan King Betok', 'description' => 'Ikan air tawar dengan karakter unik.', 'category_id' => 1, 'price' => 12000, 'img' => null, 'stock' => 12, 'slug' => 'ikan-king-betok'],
            ['name' => 'Ikan Toman', 'description' => 'Ikan predator air tawar untuk penghobi berpengalaman.', 'category_id' => 1, 'price' => 500000, 'img' => null, 'stock' => 3, 'slug' => 'ikan-toman'],
            ['name' => 'Pakan Ikan Akari', 'description' => 'Pakan ikan harian dalam kemasan.', 'category_id' => 2, 'price' => 35000, 'img' => null, 'stock' => 25, 'slug' => 'pakan-ikan-akari'],
            ['name' => 'Cacing Beku untuk Ikan', 'description' => 'Pakan tambahan untuk ikan hias.', 'category_id' => 2, 'price' => 15000, 'img' => null, 'stock' => 30, 'slug' => 'cacing-beku-untuk-ikan'],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['slug' => $product['slug']],
                [...$product, 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
