<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeds the data the Custom Corndog builder (resources/views/customer/customize.blade.php)
 * depends on. The builder reads Products in the "Custom" category and partitions them
 * into Isian / Varian / Sauce by keyword (see MenuController::customize + matchesType),
 * then maps each name to a local render asset (see $staticImageFor in the view).
 *
 * Product names below are chosen to satisfy BOTH the keyword filters and the image map,
 * and to avoid colliding with the regular menu products seeded by ProductSeeder.
 */
class CustomCorndogSeeder extends Seeder
{
    public function run(): void
    {
        $custom = Category::firstOrCreate(['name' => 'Custom']);

        // [name, price (extra), image asset]
        $components = [
            // ── Isian (Step 1) — matched by 'sosis' / 'mozza'; price not added to total ──
            ['Sosis & Mozza', 0,    'custom_sosis_mozza.png'],
            ['Full Mozza',    0,    'custom_mozza.png'],
            ['Full Sosis',    0,    'custom_sosis.png'],

            // ── Varian (Step 2) — matched by 'original' / 'potato' / 'ramen'; extra IS added ──
            ['Original',      0,    'custom_original.png'],
            ['Potato',        4000, 'custom_potato.png'],
            ['Ramen',         3000, 'custom_ramen.png'],

            // ── Sauce (Step 3) — matched by 'ketchup' / 'mayo' / 'sauce'; free ──
            ['Ketchup',       0,    'custom_ketchup.png'],
            ['Mayonnaise',    0,    'custom_mayonnaise.png'],
            ['Hot Sauce',     0,    'custom_hotsauce.png'],
            ['Cheese Sauce',  0,    'custom_cheesesauce.png'],
        ];

        foreach ($components as [$name, $price, $asset]) {
            Product::firstOrCreate(
                ['name' => $name, 'category_id' => $custom->id],
                [
                    'description'  => 'Komponen custom corndog.',
                    'price'        => $price,
                    'image'        => 'assets/img/' . $asset,
                    'stock'        => 100,
                    'is_available' => true,
                    'is_custom'    => true,
                ]
            );
        }
    }
}
