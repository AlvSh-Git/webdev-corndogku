# AJAX Pagination & Fluid Layout Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the menu page's server-rendered product cards with a 15-per-page AJAX-paginated grid driven by a new `GET /api/products` endpoint, and open all customer layout containers to a fluid `max-w-[1440px]` wrapper.

**Architecture:** A new `ProductController@catalog()` method handles server-side filtering (category, search, sort, price range) and returns `paginate(15)` JSON. The menu page JS replaces static DOM filtering with a central `loadProducts(page)` function that fires an AJAX request on every filter/sort/search change and renders both cards and pagination nav from the JSON response. Container width fixes are a targeted find-and-replace across four files.

**Tech Stack:** Laravel 11, PHP 8.2, jQuery 3.7.1, Tailwind CSS, Blade, Pest (backend tests)

---

## File Map

| File | Action | What changes |
|------|--------|--------------|
| `app/Http/Controllers/ProductController.php` | Modify | Add `catalog()` method |
| `routes/web.php` | Modify | Add `GET /api/products` route |
| `tests/Feature/ProductCatalogTest.php` | Create | Pest feature tests for the catalog endpoint |
| `resources/views/menu/index.blade.php` | Modify | Remove server-rendered cards; add `#pagination-nav`; replace JS filter/sort logic with AJAX functions |
| `resources/views/layouts/customer.blade.php` | Modify | Navbar + footer container: `max-w-6xl` → `w-full max-w-[1440px]` |
| `resources/views/cart/index.blade.php` | Modify | Two containers: `max-w-6xl` → `w-full max-w-[1440px]` |
| `resources/views/profile/index.blade.php` | Modify | One container: `max-w-5xl` → `w-full max-w-[1440px]` |

---

## Task 1: Backend — `ProductController@catalog()` and route

**Files:**
- Modify: `app/Http/Controllers/ProductController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add the `catalog()` method to `ProductController`**

Open `app/Http/Controllers/ProductController.php`. Add the following method at the end of the class, before the closing `}`:

```php
public function catalog(Request $request): \Illuminate\Http\JsonResponse
{
    $category = $request->input('category', 'Semua');
    $search   = trim((string) $request->input('search', ''));
    $sort     = $request->input('sort', 'default');
    $min      = (int) $request->input('min', 0);
    $max      = $request->input('max');
    $cats     = $request->input('cats', []);

    $q = Product::with('category')->where('is_custom', false);

    // Category filter — checkbox multi-select overrides tab selection
    if (!empty($cats)) {
        $q->whereHas('category', fn ($c) => $c->whereIn('name', (array) $cats));
    } elseif ($category && $category !== 'Semua') {
        $q->whereHas('category', fn ($c) => $c->where('name', $category));
    }

    if ($search !== '') {
        $q->where('name', 'like', "%{$search}%");
    }
    if ($min > 0) {
        $q->where('price', '>=', $min);
    }
    if ($max !== null && (int) $max > 0) {
        $q->where('price', '<=', (int) $max);
    }

    match ($sort) {
        'price-asc'  => $q->orderBy('price'),
        'price-desc' => $q->orderByDesc('price'),
        default      => $q->orderBy('category_id')->orderBy('name'),
    };

    $paginated = $q->paginate(15);

    // Map each product to include a pre-resolved image_url
    $data = $paginated->getCollection()->map(fn ($p) => [
        'id'          => $p->id,
        'name'        => $p->name,
        'description' => $p->description ?? '',
        'price'       => (int) $p->price,
        'image_url'   => $p->image
                            ? asset($p->image)
                            : asset('assets/img/CA_ORIGINAL.png'),
        'category'    => ['name' => $p->category?->name ?? ''],
    ]);

    return response()->json([
        'data'         => $data->values(),
        'current_page' => $paginated->currentPage(),
        'last_page'    => $paginated->lastPage(),
        'total'        => $paginated->total(),
        'from'         => $paginated->firstItem(),
        'to'           => $paginated->lastItem(),
    ]);
}
```

- [ ] **Step 2: Register the route in `routes/web.php`**

In `routes/web.php`, add the following line immediately after the `/store-status` route (around line 22), before the cart routes section:

```php
Route::get('/api/products', [ProductController::class, 'catalog'])->name('api.products');
```

The block should look like:

```php
Route::get('/store-status', [\App\Http\Controllers\JadwalController::class, 'getStatus'])->name('store.status');
Route::get('/api/products', [ProductController::class, 'catalog'])->name('api.products');

// ── Cart ────────────────────────────────────────────────────────
```

- [ ] **Step 3: Write the Pest feature test**

Create `tests/Feature/ProductCatalogTest.php`:

```php
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
    expect($first)->toHaveKeys(['id', 'name', 'description', 'price', 'image_url', 'category']);
    expect($first['category'])->toHaveKey('name');
});
```

> **Note:** This test requires `Category::factory()` and `Product::factory()` to exist. If they don't, check `database/factories/` — if missing, create them (see Step 4).

- [ ] **Step 4: Ensure factories exist**

Check `database/factories/` for `CategoryFactory.php` and `ProductFactory.php`.

If `CategoryFactory.php` is missing, create `database/factories/CategoryFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
        ];
    }
}
```

If `ProductFactory.php` is missing, create `database/factories/ProductFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name'        => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomElement([10000, 15000, 20000, 25000, 30000]),
            'cost_price'  => 8000,
            'stock'       => 10,
            'image'       => null,
            'is_custom'   => false,
            'is_available'=> true,
        ];
    }
}
```

Also ensure the `Category` and `Product` models have `use HasFactory;`:

In `app/Models/Category.php`, add `use Illuminate\Database\Eloquent\Factories\HasFactory;` at the top and `use HasFactory;` inside the class.

In `app/Models/Product.php`, same — add `use HasFactory;`.

- [ ] **Step 5: Run the tests**

```bash
php artisan test tests/Feature/ProductCatalogTest.php --testdox
```

Expected output — all 7 tests pass:
```
PASS  Tests\Feature\ProductCatalogTest
✓ returns paginated json with 15 items per page
✓ excludes custom products
✓ filters by category name
✓ filters by search term
✓ sorts by price ascending
✓ filters by price range
✓ includes image_url and category name in each item
```

If any test fails, fix the issue before proceeding.

- [ ] **Step 6: Smoke-test the endpoint manually**

With `php artisan serve` running (or Herd), open a browser to:

```
http://corndogku.test/api/products
```

You should see valid JSON with `data`, `current_page`, `last_page`, `total`. Try:
- `?category=Corndog+Asin` — should filter
- `?sort=price-asc` — should sort
- `?page=2` — should show page 2 (or empty `data` if fewer than 16 products)

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/ProductController.php \
        routes/web.php \
        tests/Feature/ProductCatalogTest.php \
        database/factories/CategoryFactory.php \
        database/factories/ProductFactory.php
git commit -m "feat: add ProductController@catalog AJAX endpoint with paginate(15)"
```

---

## Task 2: Menu Blade — Remove server-rendered cards, add pagination nav placeholder

**Files:**
- Modify: `resources/views/menu/index.blade.php`

This task strips out the server-side PHP/Blade product rendering so the grid starts empty and is filled by AJAX. No JS changes yet — just HTML scaffolding.

- [ ] **Step 1: Remove the inline PHP product query**

In `resources/views/menu/index.blade.php`, find and delete this entire block (lines ~362–365):

```blade
@php
    $menuProducts = \App\Models\Product::with('category')->orderBy('category_id')->get();
    $categories   = ['Semua', 'Corndog Asin', 'Corndog Manis', 'Toppoki', 'Combo', 'Es Teler Kwentel', 'Bingsoo'];
@endphp
```

Replace it with only the categories array (products are no longer needed here):

```blade
@php
    $categories = ['Semua', 'Corndog Asin', 'Corndog Manis', 'Toppoki', 'Combo', 'Es Teler Kwentel', 'Bingsoo'];
@endphp
```

- [ ] **Step 2: Remove the server-rendered product cards**

Find the `{{-- Product grid --}}` block and replace the entire `@foreach` loop inside `#product-grid` with nothing — leave the grid `<div>` empty:

Find this (lines ~394–440):

```blade
    {{-- Product grid --}}
    <div id="product-grid"
         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">

        @foreach ($menuProducts as $p)
            <div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden cursor-pointer"
                 style="box-shadow: 0 2px 12px rgba(0,0,0,0.08);"
                 data-category="{{ $p->category->name }}"
                 data-price="{{ $p->price }}"
                 data-name="{{ strtolower($p->name) }}"
                 data-id="{{ $p->id }}">

                {{-- Image: uniform crop locked into card top --}}
                <div class="overflow-hidden rounded-t-2xl">
                    <img src="{{ asset($p->image) }}"
                         alt="{{ $p->name }}"
                         class="w-full h-48 object-cover rounded-t-2xl transition-transform duration-300 hover:scale-105"
                         onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                </div>

                {{-- Text area --}}
                <div class="px-4 pt-3 pb-4 flex flex-col flex-1">
                    <p class="font-bold text-sm leading-snug" style="color: var(--color-primary);">
                        {{ $p->name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">
                        {{ $p->description }}
                    </p>
                    <div class="flex items-center justify-between mt-3 gap-2">
                        <p class="text-sm font-black" style="color: var(--color-primary);">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </p>
                        <button type="button"
                                class="btn-pesan flex-none px-3 py-1 rounded-full text-xs font-bold transition-opacity hover:opacity-80"
                                style="background-color: var(--color-accent); color: var(--color-black);"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                data-price="{{ $p->price }}"
                                data-description="{{ $p->description }}"
                                data-image="{{ asset($p->image) }}">
                            Pesan
                        </button>
                    </div>
                </div>

            </div>
        @endforeach

    </div>
```

Replace with:

```blade
    {{-- Product grid — populated by AJAX --}}
    <div id="product-grid"
         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
    </div>
```

- [ ] **Step 3: Add pagination nav placeholder**

Immediately after the closing `</div>{{-- /#product-grid --}}` and the empty-state div, add:

```blade
    {{-- Empty state --}}
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="text-5xl mb-4">🌽</div>
        <p class="font-bold text-lg" style="color: var(--color-black);">Produk tidak ditemukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau pilih kategori berbeda.</p>
    </div>

    {{-- Pagination nav — rendered by JS --}}
    <div id="pagination-nav"></div>
```

- [ ] **Step 4: Update result-count initial text**

Find:
```blade
    <span id="result-count" class="text-xs text-gray-400 font-medium">
        {{ $menuProducts->count() }} produk
    </span>
```

Replace with:
```blade
    <span id="result-count" class="text-xs text-gray-400 font-medium">
        — produk
    </span>
```

- [ ] **Step 5: Verify the page loads without PHP errors**

Navigate to `http://corndogku.test/menu`. The page should load with an empty product grid (no Blade errors). The JS hasn't been updated yet so no cards appear — that's expected.

- [ ] **Step 6: Commit**

```bash
git add resources/views/menu/index.blade.php
git commit -m "refactor(menu): remove server-rendered product cards, scaffold AJAX grid"
```

---

## Task 3: Menu JS — Core AJAX functions

**Files:**
- Modify: `resources/views/menu/index.blade.php` (the `<script>` block)

Add the four core functions (`buildProductCard`, `renderCards`, `renderPagination`, `loadProducts`) and wire the initial page load. Do not rewire the filter/sort/search triggers yet — that's Task 4.

- [ ] **Step 1: Remove `$origCards` initialization**

In the JS `/* ── State ──` section, find and delete:

```js
    /* Store original order --*/
    var $grid    = $('#product-grid');
    var $origCards = $grid.children('.product-card').clone(true);
```

Replace with just:

```js
    var $grid = $('#product-grid');
```

- [ ] **Step 2: Add `currentPage` state variable**

In the `/* ── State ──` section, add `var currentPage = 1;` so the block reads:

```js
    /* ── State ──────────────────────────────────────────── */
    var activeCat      = 'Semua';
    var searchTerm     = '';
    var sortMode       = 'default';
    var filterMinPrice = 0;
    var filterMaxPrice = null;
    var filterCats     = [];
    var currentPage    = 1;

    var $grid = $('#product-grid');
```

Note: `filterMaxPrice` is now `null` (was `Infinity`) because `null` is easier to check before including it as a query param.

- [ ] **Step 3: Add `buildProductCard(p)` function**

Add this function in the `/* ── Helpers ──` section, replacing the old `applyFilters` and `applySort` functions entirely:

```js
    /* ── Helpers ──────────────────────────────────────────── */
    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function buildProductCard(p) {
        var fallback = '{{ asset('assets/img/CA_ORIGINAL.png') }}';
        var safeName = p.name ? p.name.replace(/"/g, '&quot;') : '';
        var safeDesc = p.description ? p.description.replace(/"/g, '&quot;') : '';
        return '<div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden cursor-pointer"' +
               ' style="box-shadow:0 2px 12px rgba(0,0,0,0.08);"' +
               ' data-category="' + (p.category ? p.category.name : '') + '"' +
               ' data-price="' + p.price + '"' +
               ' data-name="' + safeName.toLowerCase() + '"' +
               ' data-id="' + p.id + '">' +
               '<div class="overflow-hidden rounded-t-2xl">' +
               '<img src="' + p.image_url + '"' +
               ' alt="' + safeName + '"' +
               ' class="w-full h-48 object-cover rounded-t-2xl transition-transform duration-300 hover:scale-105"' +
               ' onerror="this.src=\'' + fallback + '\'">' +
               '</div>' +
               '<div class="px-4 pt-3 pb-4 flex flex-col flex-1">' +
               '<p class="font-bold text-sm leading-snug" style="color:var(--color-primary);">' + p.name + '</p>' +
               '<p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">' + (p.description || '') + '</p>' +
               '<div class="flex items-center justify-between mt-3 gap-2">' +
               '<p class="text-sm font-black" style="color:var(--color-primary);">' + fmtRp(p.price) + '</p>' +
               '<button type="button"' +
               ' class="btn-pesan flex-none px-3 py-1 rounded-full text-xs font-bold transition-opacity hover:opacity-80"' +
               ' style="background-color:var(--color-accent);color:var(--color-black);"' +
               ' data-id="' + p.id + '"' +
               ' data-name="' + safeName + '"' +
               ' data-price="' + p.price + '"' +
               ' data-description="' + safeDesc + '"' +
               ' data-image="' + p.image_url + '">Pesan</button>' +
               '</div></div></div>';
    }

    function renderCards(data) {
        $grid.empty();
        $.each(data, function (_, p) {
            $grid.append(buildProductCard(p));
        });
    }

    function renderPagination(res) {
        var $nav = $('#pagination-nav').empty();
        if (res.last_page <= 1) { return; }

        var cur  = res.current_page;
        var last = res.last_page;
        var html = '<div class="flex items-center justify-center gap-1 mt-8 flex-wrap">';

        // Prev button
        var prevDisabled = (cur === 1) ? 'disabled' : '';
        var prevClass    = (cur === 1) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50';
        html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ' + prevClass + '"' +
                ' data-page="' + (cur - 1) + '" ' + prevDisabled +
                ' style="border-color:var(--color-border);">&#8592; Prev</button>';

        // Page number window: first, last, and current ±2
        var pages = [];
        for (var i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= cur - 2 && i <= cur + 2)) {
                pages.push(i);
            }
        }
        var prev = 0;
        $.each(pages, function (_, p) {
            if (prev && p - prev > 1) {
                html += '<span class="px-2 py-1.5 text-sm text-gray-400">&hellip;</span>';
            }
            var isActive = (p === cur);
            var btnStyle = isActive
                ? 'background-color:var(--color-primary);color:white;border-color:var(--color-primary);'
                : 'border-color:var(--color-border);';
            html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors' +
                    (isActive ? '' : ' hover:bg-gray-50') + '"' +
                    ' data-page="' + p + '" style="' + btnStyle + '">' + p + '</button>';
            prev = p;
        });

        // Next button
        var nextDisabled = (cur === last) ? 'disabled' : '';
        var nextClass    = (cur === last) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50';
        html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ' + nextClass + '"' +
                ' data-page="' + (cur + 1) + '" ' + nextDisabled +
                ' style="border-color:var(--color-border);">Next &#8594;</button>';

        html += '</div>';
        $nav.html(html);
    }

    function loadProducts(page) {
        currentPage = page || 1;
        var params = {
            page:     currentPage,
            category: activeCat,
            search:   searchTerm,
            sort:     sortMode,
            min:      filterMinPrice || 0,
        };
        if (filterMaxPrice) { params.max = filterMaxPrice; }
        if (filterCats.length) { params['cats[]'] = filterCats; }

        $grid.css({ opacity: '0.5', 'pointer-events': 'none' });

        $.get('/api/products', params)
            .done(function (res) {
                renderCards(res.data);
                renderPagination(res);
                $('#result-count').text(res.total + ' produk');
                $('#active-cat-label').text(activeCat);
                $('#empty-state').toggleClass('hidden', res.data.length > 0);
            })
            .always(function () {
                $grid.css({ opacity: '1', 'pointer-events': '' });
            });
    }
```

> **Important:** The `fmtRp` function appears twice in the original file (in Helpers and in the modal section). Delete the one in Helpers since you just defined it above. Keep the one in the modal section — or remove the duplicate and rely on the single definition. Make sure only one `fmtRp` function exists.

- [ ] **Step 4: Add pagination button click handler**

Find the `/* ── Init ──` section and add the pagination click handler before the init call:

```js
    /* ── Pagination click ─────────────────────────────────── */
    $(document).on('click', '.pg-btn:not([disabled])', function () {
        var page = parseInt($(this).data('page'), 10);
        if (!page) { return; }
        loadProducts(page);
        $('html, body').animate({ scrollTop: $grid.offset().top - 80 }, 200);
    });

    /* ── Init ──────────────────────────────────────────────── */
    loadProducts(1);
```

Replace the old `applyFilters()` init call with `loadProducts(1)`.

- [ ] **Step 5: Delete the now-dead `applyFilters()` and `applySort()` functions**

In the `/* ── Helpers ──` section, you replaced both functions in Step 3. Confirm they are fully removed — no `applyFilters` or `applySort` function bodies remain anywhere in the `<script>` block.

- [ ] **Step 6: Verify products load on page visit**

Navigate to `http://corndogku.test/menu`. You should see:
- Product cards appear after a brief moment (AJAX load)
- Result count updates to the real total
- Pagination nav appears below the grid if there are more than 15 products

Open browser DevTools → Network tab → filter for `/api/products` — confirm the request fires and returns 200 with JSON.

- [ ] **Step 7: Commit**

```bash
git add resources/views/menu/index.blade.php
git commit -m "feat(menu): add AJAX product loader with buildProductCard and pagination nav"
```

---

## Task 4: Menu JS — Rewire all filter/sort/search triggers

**Files:**
- Modify: `resources/views/menu/index.blade.php` (the `<script>` block)

All triggers that previously called `applyFilters()` or `applySort()` now call `loadProducts(1)`. Search gets a 300 ms debounce.

- [ ] **Step 1: Rewire the category tab click handler**

Find:
```js
    /* ── Category tabs ────────────────────────────────────── */
    $(document).on('click', '.cat-tab', function () {
        activeCat = $(this).data('cat');

        $('.cat-tab').each(function () {
            $(this).removeClass('active')
                   .css({ 'background-color': 'var(--color-white)', 'color': 'var(--color-black)', 'border-color': 'var(--color-border)' });
        });
        $(this).addClass('active')
               .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'border-color': 'var(--color-primary)' });

        $('#active-cat-label').text(activeCat);
        applyFilters();
    });
```

Replace with:
```js
    /* ── Category tabs ────────────────────────────────────── */
    $(document).on('click', '.cat-tab', function () {
        activeCat = $(this).data('cat');

        $('.cat-tab').each(function () {
            $(this).removeClass('active')
                   .css({ 'background-color': 'var(--color-white)', 'color': 'var(--color-black)', 'border-color': 'var(--color-border)' });
        });
        $(this).addClass('active')
               .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'border-color': 'var(--color-primary)' });

        loadProducts(1);
    });
```

- [ ] **Step 2: Rewire the navbar search with debounce**

Find:
```js
    /* ── Navbar search ─────────────────────────────────────── */
    $('#navbar-search').on('input', function () {
        searchTerm = $.trim($(this).val()).toLowerCase();
        applyFilters();
    });
```

Replace with:
```js
    /* ── Navbar search ─────────────────────────────────────── */
    var searchDebounce = null;
    $('#navbar-search').on('input', function () {
        searchTerm = $.trim($(this).val()).toLowerCase();
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            loadProducts(1);
        }, 300);
    });
```

- [ ] **Step 3: Rewire sort apply and sort reset**

Find:
```js
    /* ── Sort apply ────────────────────────────────────────── */
    $('#btn-sort-apply').on('click', function () {
        sortMode = $('input[name="sort-option"]:checked').val() || 'default';
        closeAllDropdowns();
        applySort();
    });

    /* ── Sort reset ────────────────────────────────────────── */
    $('#btn-sort-reset').on('click', function () {
        $('input[name="sort-option"]').prop('checked', false);
        sortMode = 'default';
        updateSortUI();
        closeAllDropdowns();
        applySort();
    });
```

Replace with:
```js
    /* ── Sort apply ────────────────────────────────────────── */
    $('#btn-sort-apply').on('click', function () {
        sortMode = $('input[name="sort-option"]:checked').val() || 'default';
        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Sort reset ────────────────────────────────────────── */
    $('#btn-sort-reset').on('click', function () {
        $('input[name="sort-option"]').prop('checked', false);
        sortMode = 'default';
        updateSortUI();
        closeAllDropdowns();
        loadProducts(1);
    });
```

- [ ] **Step 4: Rewire filter apply and filter reset**

Find:
```js
    /* ── Filter apply ──────────────────────────────────────── */
    $('#btn-filter-apply').on('click', function () {
        var minVal     = $('#filter-price-min').val();
        var maxVal     = $('#filter-price-max').val();
        filterMinPrice = minVal ? parseInt(minVal, 10) : 0;
        filterMaxPrice = maxVal ? parseInt(maxVal, 10) : Infinity;

        filterCats = [];
        $('.filter-cat-check:checked').each(function () {
            filterCats.push($(this).val());
        });

        closeAllDropdowns();
        applyFilters();
    });

    /* ── Filter reset ──────────────────────────────────────── */
    $('#btn-filter-reset').on('click', function () {
        filterMinPrice = 0;
        filterMaxPrice = Infinity;
        filterCats     = [];
        $('#filter-price-min, #filter-price-max').val('');
        $('.filter-cat-check').prop('checked', false);
        $('.price-pill').css({ 'border-color': 'var(--color-border)', 'background-color': '', 'color': '#555' });
        closeAllDropdowns();
        applyFilters();
    });
```

Replace with:
```js
    /* ── Filter apply ──────────────────────────────────────── */
    $('#btn-filter-apply').on('click', function () {
        var minVal     = $('#filter-price-min').val();
        var maxVal     = $('#filter-price-max').val();
        filterMinPrice = minVal ? parseInt(minVal, 10) : 0;
        filterMaxPrice = maxVal ? parseInt(maxVal, 10) : null;

        filterCats = [];
        $('.filter-cat-check:checked').each(function () {
            filterCats.push($(this).val());
        });

        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Filter reset ──────────────────────────────────────── */
    $('#btn-filter-reset').on('click', function () {
        filterMinPrice = 0;
        filterMaxPrice = null;
        filterCats     = [];
        $('#filter-price-min, #filter-price-max').val('');
        $('.filter-cat-check').prop('checked', false);
        $('.price-pill').css({ 'border-color': 'var(--color-border)', 'background-color': '', 'color': '#555' });
        closeAllDropdowns();
        loadProducts(1);
    });
```

- [ ] **Step 5: Full interaction test**

Open `http://corndogku.test/menu` and verify each interaction fires a new AJAX request (watch DevTools Network tab):

1. Click a category tab → cards refresh, count updates
2. Type in the search bar → after 300 ms, cards filter by name
3. Open Sort dropdown, select "Harga: Rendah ke Tinggi", click Terapkan → cards re-order by price
4. Open Filter dropdown, set a max price, click Terapkan → cards filtered
5. Click Reset in Filter → all cards reappear
6. Click a pagination number (if visible) → page changes, scroll jumps to grid top
7. Click "Pesan" on any card → product modal opens with correct name/price/image

- [ ] **Step 6: Commit**

```bash
git add resources/views/menu/index.blade.php
git commit -m "feat(menu): wire all filter/sort/search triggers to AJAX loadProducts"
```

---

## Task 5: Fluid Responsiveness — All customer layout containers

**Files:**
- Modify: `resources/views/layouts/customer.blade.php`
- Modify: `resources/views/cart/index.blade.php`
- Modify: `resources/views/profile/index.blade.php`

- [ ] **Step 1: Fix `layouts/customer.blade.php` navbar container**

Find (line ~19):
```html
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16
                flex items-center justify-between gap-6">
```

Replace with:
```html
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-16
                flex items-center justify-between gap-6">
```

- [ ] **Step 2: Fix `layouts/customer.blade.php` footer container**

Find (line ~126):
```html
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
```

Replace with:
```html
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
```

- [ ] **Step 3: Fix `cart/index.blade.php` hero container**

Find (line ~11):
```html
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-6 flex items-center justify-between">
```

Replace with:
```html
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-6 flex items-center justify-between">
```

- [ ] **Step 4: Fix `cart/index.blade.php` main content container**

Find (line ~31):
```html
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-8">
```

Replace with:
```html
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-8">
```

- [ ] **Step 5: Fix `profile/index.blade.php` main wrapper**

Find (line ~7):
```html
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">
```

Replace with:
```html
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">
```

- [ ] **Step 6: Verify layouts on different viewport widths**

Open each of these pages in the browser and use DevTools → Toggle Device Toolbar to check three widths:
- **375 px** (mobile) — content should stack, no overflow
- **1280 px** (standard laptop) — content should fill width without feeling cramped
- **1920 px** (wide desktop) — content should center inside the 1440 px boundary with visible margin on both sides; no text or card should stretch full-screen edge-to-edge

Pages to check: `/cart`, `/profile`, `/menu` (already 1440), `/` (already 1440).

- [ ] **Step 7: Commit**

```bash
git add resources/views/layouts/customer.blade.php \
        resources/views/cart/index.blade.php \
        resources/views/profile/index.blade.php
git commit -m "style: open customer layout containers to fluid max-w-[1440px]"
```

---

## Self-Review Checklist

- [x] **Spec § 1 (Backend endpoint)** — covered in Task 1
- [x] **Spec § 2a (Remove server-side rendering)** — covered in Task 2 Steps 1–2
- [x] **Spec § 2b–2c (JS state + loadProducts)** — covered in Task 3 Steps 1–2, 4
- [x] **Spec § 2d–2e (buildProductCard + renderCards)** — covered in Task 3 Step 3
- [x] **Spec § 2f (renderPagination)** — covered in Task 3 Steps 3–4
- [x] **Spec § 2g (trigger rewiring)** — covered in Task 4
- [x] **Spec § 2h (initial load)** — covered in Task 3 Step 4 (`loadProducts(1)`)
- [x] **Spec § 3 (fluid containers)** — covered in Task 5
- [x] **Spec § 4 (route)** — covered in Task 1 Step 2
- [x] **Spec Risk: filterCats as `cats[]`** — handled in `loadProducts()` (`params['cats[]'] = filterCats`) and `catalog()` (`$cats = $request->input('cats', [])`)
- [x] **Spec Risk: debounce on search** — 300 ms debounce in Task 4 Step 2
- [x] **Spec Risk: image_url** — resolved via `asset($p->image)` in `catalog()` Step 1
- [x] **No `Infinity` in filter state** — replaced with `null` throughout (Task 3 Step 2, Task 4 Step 4)
- [x] **Duplicate `fmtRp`** — Task 3 Step 3 flags this for removal
