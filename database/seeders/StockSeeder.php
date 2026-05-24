<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $count = Product::query()->update(['stock' => 50, 'is_available' => true]);
        $this->command->info("Updated {$count} products → stock = 50, is_available = true");

        Product::select('id', 'name', 'stock', 'is_available')->get()->each(function ($p) {
            $this->command->line("  [{$p->id}] {$p->name} — stock:{$p->stock} available:" . ($p->is_available ? 'yes' : 'no'));
        });
    }
}
