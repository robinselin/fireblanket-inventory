<?php

namespace Database\Seeders;

use App\Models\Pack;
use Illuminate\Database\Seeder;

class PackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];

        foreach ($packSizes as $size) {
            Pack::create([
                'size' => $size,
                'name' => "FB PACK {$size}",
                'description' => "Fire Blanket Pack containing {$size} units",
            ]);
        }
    }
}
