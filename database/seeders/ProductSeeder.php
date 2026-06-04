<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $products = [
            // Toppoki
            [
                'category' => 'Toppoki',
                'nama'     => 'TP_KOREAN',
                'name'     => 'Toppoki Korean',
                'description' => 'Tteok kenyal dengan saus gochujang khas Korea yang gurih pedas, disajikan dengan sosis dan odeng hangat.',
                'price'    => 23000,
            ],
            [
                'category' => 'Toppoki',
                'nama'     => 'TP_ROSE',
                'name'     => 'Toppoki Rose',
                'description' => 'Perpaduan creamy susu dan saus gochujang khas Korea dengan tteok kenyal, sosis, dan odeng yang creamy gurih.',
                'price'    => 26000,
            ],

            // Corndog Asin
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_ORIGINAL',
                'name'     => 'Corndog Original',
                'description' => 'Perpaduan sosis dan mozzarella lumer dengan balutan tepung crispy khas Korean corndog.',
                'price'    => 16000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_FULL_SAUSAGE',
                'name'     => 'Corndog Full Sausage',
                'description' => 'Sosis utuh dengan lapisan crispy gurih dan saus favorit pilihanmu.',
                'price'    => 17000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_FULL_MOZZA',
                'name'     => 'Corndog Full Mozza',
                'description' => 'Mozzarella full lumer dengan tekstur crispy di luar dan cheesy di dalam.',
                'price'    => 17000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_MOZZA_POTATO',
                'name'     => 'Corndog Mozza Potato',
                'description' => 'Corndog mozzarella dengan balutan potongan kentang crispy yang gurih dan crunchy.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_RAMEN_MIX',
                'name'     => 'Corndog Ramen Mix',
                'description' => 'Kombinasi sosis dan mozzarella dengan topping ramen crispy yang unik dan gurih.',
                'price'    => 18000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_ORIGINAL_BUBBLE',
                'name'     => 'Corndog Original Bubble',
                'description' => 'Perpaduan mozzarella dan sosis dengan bubble crumbs renyah yang bikin tekstur makin crunchy.',
                'price'    => 19000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_CHEETOS',
                'name'     => 'Corndog Cheetos',
                'description' => 'Corndog sosis mozzarella dengan rasa cheetos gurih pedas yang super crunchy.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_DOUBLE_CHEESE',
                'name'     => 'Corndog Double Cheese',
                'description' => 'Full mozzarella dengan saus keju dan taburan keju melimpah yang creamy dan lumer.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_SQUID_ORI',
                'name'     => 'Corndog Squid Original',
                'description' => 'Corndog sosis mozzarella dengan model sosis seperti squid crispy khas yang gurih dan unik.',
                'price'    => 16000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_SQUID_NORI',
                'name'     => 'Corndog Squid Nori',
                'description' => 'Perpaduan sosis mozzarella dengan model sosis seperti squid dan taburan nori rumput laut gurih.',
                'price'    => 18000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_SQUID_POTATO',
                'name'     => 'Corndog Squid Potato',
                'description' => 'Corndog berbentuk squid dengan balutan kentang crispy yang crunchy di luar dan mozzarella lumer di dalam. Gurih dan super satisfying.',
                'price'    => 17000,
            ],
            [
                'category' => 'Corndog Asin',
                'nama'     => 'CA_SQUID_RAMEN_MIX',
                'name'     => 'Corndog Squid Ramen Mix',
                'description' => 'Corndog berbentuk squid dengan topping ramen crispy yang unik dan penuh tekstur. Crunchy, savory, dan bikin nagih.',
                'price'    => 17000,
            ],

            // Corndog Manis
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_CHOCO_CHRUNCH_CHEESE',
                'name'     => 'Corndog Choco Crunch Cheese',
                'description' => 'Corndog mozzarella dengan glaze coklat premium dan taburan keju parut melimpah.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_MILK_CHRUNCH_BISKUIT',
                'name'     => 'Corndog Milk Crunch Biskuit',
                'description' => 'Corndog mozzarella dengan glaze susu creamy dan topping biskuit crunchy melimpah.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_GREENTEA_CHRUNCHY_BISKUIT',
                'name'     => 'Corndog Greentea Crunchy Biskuit',
                'description' => 'Corndog mozzarella dengan glaze greentea dan taburan biskuit matcha crunchy yang manis gurih.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_TARO_CHEESE',
                'name'     => 'Corndog Taro Cheese',
                'description' => 'Perpaduan glaze taro creamy dengan mozzarella lumer dan taburan keju parut.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_TIRAMISU_BISKUIT',
                'name'     => 'Corndog Tiramisu Biskuit',
                'description' => 'Corndog mozzarella dengan glaze tiramisu creamy dan topping biskuit crunchy ala dessert.',
                'price'    => 20000,
            ],
            [
                'category' => 'Corndog Manis',
                'nama'     => 'CM_STRAWBERRY_BISKUIT',
                'name'     => 'Corndog Strawberry Biskuit',
                'description' => 'Glaze strawberry manis segar dengan taburan biskuit crunchy dan mozzarella lumer.',
                'price'    => 20000,
            ],

            // Combo
            [
                'category' => 'Combo',
                'nama'     => 'CB_01',
                'name'     => 'Combo 1',
                'description' => 'Kombinasi Korean Toppoki dan pilihan corndog original, full mozzarella, atau full sausage.',
                'price'    => 35000,
            ],
            [
                'category' => 'Combo',
                'nama'     => 'CB_02',
                'name'     => 'Combo 2',
                'description' => 'Korean Toppoki dipadukan dengan corndog full mozza potato, ori potato, atau ori bubble yang super crunchy.',
                'price'    => 38000,
            ],
            [
                'category' => 'Combo',
                'nama'     => 'CB_03',
                'name'     => 'Combo 3',
                'description' => 'Rose Toppoki creamy dengan pilihan corndog original, full mozzarella, atau full sausage.',
                'price'    => 37000,
            ],
            [
                'category' => 'Combo',
                'nama'     => 'CB_04',
                'name'     => 'Combo 4',
                'description' => 'Rose Toppoki creamy dipadukan dengan corndog full mozza potato, ori potato, atau ori bubble.',
                'price'    => 40000,
            ],

            // Es Teler Kwentel
            [
                'category' => 'Es Teler Kwentel',
                'nama'     => 'ETK_ORI',
                'name'     => 'Es Teler Original',
                'description' => 'Perpaduan es serut, alpukat, nangka, jelly, mutiara, nata de coco, dan gula aren yang segar.',
                'price'    => 15000,
            ],
            [
                'category' => 'Es Teler Kwentel',
                'nama'     => 'ETK_ES_KRIM_VANILLA',
                'name'     => 'Es Teler Es Krim Vanilla',
                'description' => 'Es teler segar dengan tambahan es krim vanila creamy yang bikin makin nikmat.',
                'price'    => 18000,
            ],
            [
                'category' => 'Es Teler Kwentel',
                'nama'     => 'ETK_DURIAN',
                'name'     => 'Es Teler Durian',
                'description' => 'Es teler segarl dengan tambahan durian legit dan aroma khas yang menggoda.',
                'price'    => 28000,
            ],
            [
                'category' => 'Es Teler Kwentel',
                'nama'     => 'ETK_SPECIAL',
                'name'     => 'Es Teler Special',
                'description' => 'Kombinasi lengkap es teler, durian, dan es krim vanila dalam satu mangkuk spesial.',
                'price'    => 31000,
            ],

            // Bingsoo
            [
                'category' => 'Bingsoo',
                'nama'     => 'BS_MANGO_CREAMY',
                'name'     => 'Bingsoo Mango Creamy',
                'description' => 'Bingsu es serut Korea dengan mangga segar, jelly mangga, nata de coco, dan es krim creamy.',
                'price'    => 18000,
            ],
            [
                'category' => 'Bingsoo',
                'nama'     => 'BS_STRAWBERRY_CREAMY',
                'name'     => 'Bingsoo Strawberry Creamy',
                'description' => 'Bingsu segar dengan potongan stroberi, pudding strawberry, nata de coco, dan es krim vanila.',
                'price'    => 18000,
            ],
            [
                'category' => 'Bingsoo',
                'nama'     => 'BS_COOKIES_CREAM',
                'name'     => 'Bingsoo Cookies & Cream',
                'description' => 'Perpaduan es serut, oreo, susu coklat, coco crunch, dan es krim yang creamy dan crunchy.',
                'price'    => 18000,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['name' => $data['name']],
                [
                    'category_id'  => $categories[$data['category']],
                    'description'  => $data['description'],
                    'price'        => $data['price'],
                    'image'        => 'assets/img/' . $data['nama'] . '.png',
                    'stock'        => 50,
                    'is_available' => true,
                    'is_custom'    => false,
                ]
            );
        }

        // Custom Corndog ingredients — consumed by the Custom Corndog builder
        // (see CustomCorndogSeeder for the full rationale on names + image mapping).
        $customCategoryId = $categories['Custom'] ?? Category::firstOrCreate(['name' => 'Custom'])->id;

        $customComponents = [
            // Isian (Step 1) — matched by 'sosis' / 'mozza'; extra price not added.
            ['Sosis & Mozza', 0,    'custom_sosis_mozza.png'],
            ['Full Mozza',    0,    'custom_mozza.png'],
            ['Full Sosis',    0,    'custom_sosis.png'],

            // Varian (Step 2) — matched by 'original' / 'potato' / 'ramen'; extra IS added.
            ['Original',      0,    'custom_original.png'],
            ['Potato',        4000, 'custom_potato.png'],
            ['Ramen',         3000, 'custom_ramen.png'],

            // Sauce (Step 3) — matched by 'ketchup' / 'mayo' / 'sauce'; free.
            ['Ketchup',       0,    'custom_ketchup.png'],
            ['Mayonnaise',    0,    'custom_mayonnaise.png'],
            ['Hot Sauce',     0,    'custom_hotsauce.png'],
            ['Cheese Sauce',  0,    'custom_cheesesauce.png'],
        ];

        foreach ($customComponents as [$name, $price, $asset]) {
            Product::firstOrCreate(
                ['name' => $name, 'category_id' => $customCategoryId],
                [
                    'description'  => 'Komponen custom corndog.',
                    'price'        => $price,
                    'image'        => 'assets/img/' . $asset,
                    'stock'        => 50,
                    'is_available' => true,
                    'is_custom'    => true,
                ]
            );
        }
    }
}
