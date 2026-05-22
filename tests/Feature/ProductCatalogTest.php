<?php

use App\Models\Category;
use App\Models\Product;

beforeEach(function () {
    // Create two categories and some products
    $asin = Category::factory()->create(['name' => 'Corndog Asin']);
    $manis = Category::factory()->create(['name' => 'Corndog Manis']);

    Product::factory()->count(10)->create(['category_id' => $asin->id,  'is_custom' => false, 'price' => 15000]);
    Product::factory()->count(8)->create(['category_id'  => $manis->id, 'is_custom' => false, 'price' => 20000]);
    Product::factory()->count(3)->create(['category_id'  => $asin->id,  'is_custom' => true,  'price' => 30000]);
});

it('returns paginated json with 15 items per page', function () {
    $response = $this->getJson('/api/products');

    $response->assertOk()
             ->assertJsonStructure(['data', 'current_page', 'last_page', 'total', 'from', 'to'])
             ->assertJsonPath('current_page', 1);

    expect(count($response->json('data')))->toBeLessThanOrEqual(15);
});

it('excludes custom products', function () {
    $response = $this->getJson('/api/products');

    $response->assertOk();
    expect($response->json('total'))->toBe(18); // 10 + 8 non-custom
});

it('filters by category name', function () {
    $response = $this->getJson('/api/products?category=Corndog+Asin');

    $response->assertOk();
    expect($response->json('total'))->toBe(10);
});

it('filters by search term', function () {
    Product::factory()->create([
        'category_id' => Category::first()->id,
        'name'        => 'Spesial Mozza',
        'is_custom'   => false,
        'price'       => 18000,
    ]);

    $response = $this->getJson('/api/products?search=Mozza');

    $response->assertOk();
    expect($response->json('total'))->toBe(1);
    expect($response->json('data.0.name'))->toBe('Spesial Mozza');
});

it('sorts by price ascending', function () {
    $response = $this->getJson('/api/products?sort=price-asc');

    $response->assertOk();
    $prices = collect($response->json('data'))->pluck('price')->all();
    expect($prices)->toBe(collect($prices)->sort()->values()->all());
});

it('filters by price range', function () {
    $response = $this->getJson('/api/products?min=16000&max=21000');

    $response->assertOk();
    foreach ($response->json('data') as $item) {
        expect($item['price'])->toBeGreaterThanOrEqual(16000)
                              ->toBeLessThanOrEqual(21000);
    }
});

it('includes image_url and category name in each item', function () {
    $response = $this->getJson('/api/products');

    $response->assertOk();
    $first = $response->json('data.0');
    expect($first)->toHaveKeys(['id', 'name', 'description', 'price', 'image_url', 'is_available', 'stock', 'category']);
    expect($first['category'])->toHaveKey('name');
});
