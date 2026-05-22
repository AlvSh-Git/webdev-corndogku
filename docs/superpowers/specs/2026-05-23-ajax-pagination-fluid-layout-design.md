# Design: AJAX Pagination & Fluid Layout Refactor

**Date:** 2026-05-23  
**Scope:** Menu page AJAX pagination (15/page, server-side filtering) + fluid `max-w-[1440px]` containers across all customer views.

---

## 1. Backend — Products API Endpoint

**Route:** `GET /api/products` in `routes/web.php`  
**Controller:** New method `ProductController@catalog` — add to the existing `ProductController` class.

**Query parameters:**

| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `category` | string | `Semua` | Category name; `Semua` = all |
| `search` | string | `` | Name substring filter |
| `sort` | string | `default` | `price-asc`, `price-desc`, or `default` (category_id order) |
| `min` | int | 0 | Min price filter |
| `max` | int | — | Max price filter (omitted = no upper bound) |

**Eloquent query:**
```php
$q = Product::with('category');

if ($category && $category !== 'Semua') {
    $q->whereHas('category', fn($c) => $c->where('name', $category));
}
if ($search) {
    $q->where('name', 'like', "%{$search}%");
}
if ($min > 0) {
    $q->where('price', '>=', $min);
}
if ($max) {
    $q->where('price', '<=', $max);
}

match($sort) {
    'price-asc'  => $q->orderBy('price'),
    'price-desc' => $q->orderByDesc('price'),
    default      => $q->orderBy('category_id'),
};

$paginated = $q->paginate(15);
```

**JSON response shape:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Corndog Original",
      "description": "...",
      "price": 15000,
      "image": "assets/img/CA_ORIGINAL.png",
      "image_url": "http://localhost/assets/img/CA_ORIGINAL.png",
      "category": { "name": "Corndog Asin" }
    }
  ],
  "current_page": 1,
  "last_page": 3,
  "total": 42,
  "from": 1,
  "to": 15
}
```

The `image_url` field is the pre-resolved `asset()` URL so the frontend doesn't need to know the app base URL.

---

## 2. Frontend — Menu Page Refactor (`menu/index.blade.php`)

### 2a. Remove server-side rendering

Remove the inline `@php $menuProducts = ...->get() @endphp` block and the entire `@foreach ($menuProducts as $p)` loop. The product grid `#product-grid` starts empty.

The result-count span initial text is also removed; it will be populated by the first AJAX response.

### 2b. JS state

Keep all existing state variables unchanged:

```js
var activeCat      = 'Semua';
var searchTerm     = '';
var sortMode       = 'default';
var filterMinPrice = 0;
var filterMaxPrice = null;   // null = no upper bound
var filterCats     = [];
var currentPage    = 1;
```

Remove `$origCards` (no longer needed — sort reset re-fetches from API).

### 2c. `loadProducts(page)` function

Central function called by all triggers:

```js
function loadProducts(page) {
    currentPage = page || 1;
    var params = {
        page:     currentPage,
        category: activeCat,
        search:   searchTerm,
        sort:     sortMode,
        min:      filterMinPrice || 0,
    };
    if (filterMaxPrice) params.max = filterMaxPrice;

    // Loading state
    $grid.css('opacity', '0.5').css('pointer-events', 'none');

    $.get('/api/products', params, function (res) {
        renderCards(res.data);
        renderPagination(res);
        var shown = res.data.length;
        $('#result-count').text(res.total + ' produk');
        $('#active-cat-label').text(activeCat);
        $('#empty-state').toggleClass('hidden', shown > 0);
    }).always(function () {
        $grid.css('opacity', '1').css('pointer-events', '');
    });
}
```

### 2d. `buildProductCard(p)` function

Constructs the same card HTML currently rendered by Blade, using `p.image_url` for the image src:

```js
function buildProductCard(p) {
    var fallback = '/assets/img/CA_ORIGINAL.png';
    var price    = 'Rp ' + Math.round(p.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return `
        <div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden cursor-pointer"
             style="box-shadow:0 2px 12px rgba(0,0,0,0.08);"
             data-category="${p.category?.name ?? ''}"
             data-price="${p.price}"
             data-name="${(p.name ?? '').toLowerCase()}"
             data-id="${p.id}">
            <div class="overflow-hidden rounded-t-2xl">
                <img src="${p.image_url}"
                     alt="${p.name}"
                     class="w-full h-48 object-cover rounded-t-2xl transition-transform duration-300 hover:scale-105"
                     onerror="this.src='${fallback}'">
            </div>
            <div class="px-4 pt-3 pb-4 flex flex-col flex-1">
                <p class="font-bold text-sm leading-snug" style="color:var(--color-primary);">${p.name}</p>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">${p.description ?? ''}</p>
                <div class="flex items-center justify-between mt-3 gap-2">
                    <p class="text-sm font-black" style="color:var(--color-primary);">${price}</p>
                    <button type="button"
                            class="btn-pesan flex-none px-3 py-1 rounded-full text-xs font-bold transition-opacity hover:opacity-80"
                            style="background-color:var(--color-accent);color:var(--color-black);"
                            data-id="${p.id}"
                            data-name="${p.name}"
                            data-price="${p.price}"
                            data-description="${p.description ?? ''}"
                            data-image="${p.image_url}">
                        Pesan
                    </button>
                </div>
            </div>
        </div>`;
}
```

### 2e. `renderCards(data)` function

```js
function renderCards(data) {
    $grid.empty();
    $.each(data, function (_, p) {
        $grid.append(buildProductCard(p));
    });
}
```

### 2f. `renderPagination(res)` function

Appended to `#pagination-nav` (new `<div>` placed below `#product-grid`):

- Hidden when `last_page === 1`
- Shows `← Prev` (disabled on page 1), page number buttons (window: current ±2 with ellipsis), `Next →` (disabled on last page)
- Current page button styled with `background-color: var(--color-primary); color: white`

```js
function renderPagination(res) {
    var $nav = $('#pagination-nav').empty();
    if (res.last_page <= 1) return;

    var cur  = res.current_page;
    var last = res.last_page;

    var html = '<div class="flex items-center justify-center gap-1 mt-8 flex-wrap">';

    // Prev
    html += `<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ${cur === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50'}" data-page="${cur - 1}" ${cur === 1 ? 'disabled' : ''} style="border-color:var(--color-border);">← Prev</button>`;

    // Page numbers (window ±2)
    var pages = [];
    for (var i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= cur - 2 && i <= cur + 2)) {
            pages.push(i);
        }
    }
    var prev = 0;
    pages.forEach(function (p) {
        if (prev && p - prev > 1) {
            html += '<span class="px-2 py-1.5 text-sm text-gray-400">…</span>';
        }
        var active = (p === cur);
        html += `<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ${active ? '' : 'hover:bg-gray-50'}"
                     data-page="${p}"
                     style="${active ? 'background-color:var(--color-primary);color:white;border-color:var(--color-primary);' : 'border-color:var(--color-border);'}">
                     ${p}
                 </button>`;
        prev = p;
    });

    // Next
    html += `<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ${cur === last ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50'}" data-page="${cur + 1}" ${cur === last ? 'disabled' : ''} style="border-color:var(--color-border);">Next →</button>`;

    html += '</div>';
    $nav.html(html);
}
```

Pagination button click:
```js
$(document).on('click', '.pg-btn:not([disabled])', function () {
    loadProducts(parseInt($(this).data('page'), 10));
    $('html, body').animate({ scrollTop: $grid.offset().top - 80 }, 200);
});
```

### 2g. Trigger updates

Replace all existing `applyFilters()` / `applySort()` call sites with `loadProducts(1)`:

- Category tab click → `loadProducts(1)`
- Navbar search `input` event (debounced 300 ms) → `loadProducts(1)`
- Sort apply → `loadProducts(1)`
- Sort reset → `sortMode = 'default'; loadProducts(1)`
- Filter apply → read values, then `loadProducts(1)`
- Filter reset → reset values, then `loadProducts(1)`

Remove `applyFilters()`, `applySort()`, and `$origCards` entirely.

### 2h. Initial load

Replace `applyFilters()` call at the bottom of `$(function(){...})` with `loadProducts(1)`.

---

## 3. Fluid Responsiveness Changes

### `layouts/customer.blade.php`

Navbar inner container: `max-w-6xl mx-auto px-4 sm:px-6 lg:px-8` → `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8`

### `cart/index.blade.php`

Both `max-w-6xl mx-auto px-4 sm:px-6 lg:px-8` occurrences → `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8`

### `profile/index.blade.php`

`max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20` → `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20`

### `customize.blade.php`

Identify main content wrapper and apply `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8`.

### `welcome.blade.php` / `menu/index.blade.php`

Already use `max-w-[1440px]` — no changes needed to container widths. Verify product grid retains `grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5`.

---

## 4. Route Addition

In `routes/web.php`, add before the auth routes:

```php
Route::get('/api/products', [ProductController::class, 'catalog'])->name('api.products');
```

---

## 5. What Is NOT Changing

- The product detail modal (open via `.btn-pesan`) — same structure, same jQuery handlers
- Cart add / order-now AJAX flows
- Store-closed banner logic
- Category tab UI (still PHP-rendered in Blade — only the product grid itself is AJAX)
- Footer
- All other customer views not mentioned above (no scope creep)

---

## 6. Risk Notes

- **`filterCats` (checkbox filter)** — The dropdown filter has category checkboxes that overlap with the tab-based `activeCat`. Resolution: when `filterCats` is non-empty, send it as a `cats[]` array param to the backend; the backend applies `whereHas('category', fn($c) => $c->whereIn('name', $cats))` and ignores the `category` param. When empty, use `category` (the tab) as before.
- **Debounce on search** — Without debounce, every keystroke fires an AJAX request. Add a 300 ms `setTimeout` debounce on the `#navbar-search` input handler.
- **`image_url`** — The backend must call `asset($product->image)` in the controller or accessor so JS receives an absolute URL, not a relative path like `assets/img/...`.
