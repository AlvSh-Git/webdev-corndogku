<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Corndog Asin',
            'Corndog Manis',
            'Toppoki',
            'Combo',
            'Es Teler Kwentel',
            'Bingsoo',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
