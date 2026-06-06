@extends('layouts.app')

@section('title', 'Product Management')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if(session('success'))
<div id="flash-success"
     class="mb-4 px-4 py-3 rounded-xl text-sm font-medium"
     style="background:#dcfce7;color:#166534;">
    {{ session('success') }}
</div>
<script>setTimeout(function(){ $('#flash-success').fadeOut(400); }, 3000);</script>
@endif

{{-- Page Header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="text-4xl font-black tracking-tight" style="color: var(--color-black);">PRODUCT</h1>

    <div class="flex items-center gap-3 flex-1 max-w-xl">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input type="search" id="search-products" placeholder="Search products..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm border border-gray-200
                          bg-white focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <select id="filter-category"
                class="px-4 py-2.5 rounded-xl text-sm border border-gray-200 bg-white
                       focus:outline-none focus:ring-2 focus:ring-red-200 min-w-[160px]">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Product Grid --}}
<div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">

    {{-- Add Menu card - Background Merah --}}
    <button id="btn-add-product" type="button"
            class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 transition-all hover:opacity-90"
            style="background-color: #A6171C; border-color: #A6171C; min-h-[220px];">
        
        {{-- Lingkaran Ikon - Dibuat putih transparan agar ikon + terlihat jelas --}}
        <span class="flex items-center justify-center w-12 h-12 rounded-full"
            style="background-color: rgba(255, 255, 255, 0.2);">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round">
                <path d="M12 4v16M4 12h16"/>
            </svg>
        </span>
        
        {{-- Teks Putih --}}
        <span class="text-sm font-bold" style="color: #FFFFFF;">Add Menu</span>
    </button>

    @foreach($products as $product)
    <div class="product-card bg-white rounded-2xl relative overflow-visible {{ !$product->is_available || $product->stock <= 0 ? 'opacity-60' : '' }}"
         data-name="{{ strtolower($product->name) }}"
         data-category="{{ $product->category?->name }}"
         data-id="{{ $product->id }}"
         style="border:1px solid var(--color-border);box-shadow:var(--shadow-card);">

        {{-- Edit / Delete --}}
        <div class="absolute -top-2 -right-2 flex items-center gap-1.5 z-10">
            <button type="button"
                    class="btn-edit w-10 h-10 flex items-center justify-center rounded-lg bg-white shadow-sm
                           border border-gray-100 hover:bg-gray-50 transition-colors"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-category-id="{{ $product->category_id }}"
                    data-price="{{ $product->price }}"
                    data-cost="{{ $product->cost_price }}"
                    data-stock="{{ $product->stock }}"
                    data-low-stock="{{ $product->low_stock ?? 0 }}"
                    data-available="{{ $product->is_available ? '1' : '0' }}"
                    data-desc="{{ $product->description }}"
                    data-image="{{ $product->image ? asset($product->image) : '' }}"
                    title="Edit product">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     style="color:var(--color-primary);">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button type="button"
                    class="btn-delete w-10 h-10 flex items-center justify-center rounded-lg bg-white shadow-sm
                           border border-gray-100 hover:bg-red-50 transition-colors"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    title="Delete product">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     style="color:var(--color-primary);">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                </svg>
            </button>
        </div>

        {{-- Image --}}
        <div class="flex items-center justify-center pt-7 pb-2 px-4">
            <div class="w-28 h-28 rounded-full overflow-hidden flex items-center justify-center"
                 style="background-color:#FDECD8;">
                <img src="{{ $product->image ? asset($product->image) : asset('assets/img/CA_ORIGINAL.png') }}"
                     alt="{{ $product->name }}"
                     class="w-24 h-24 object-contain">
            </div>
        </div>

        {{-- Info --}}
        <div class="px-4 pb-4">
            <h3 class="font-bold text-sm leading-snug" style="color:var(--color-primary);">
                {{ $product->name }}
            </h3>
            <p class="text-[11px] text-gray-500 mt-1 leading-relaxed"
               style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $product->description ?: ($product->category?->name ?? '') }}
            </p>
            <div class="flex items-center justify-between mt-3">
                @if(!$product->is_available || $product->stock <= 0)
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                          style="background-color:var(--color-status-inactive-bg);color:var(--color-status-inactive-text);">
                        Habis
                    </span>
                @elseif($product->stock <= 10)
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                          style="background-color:#fef9c3;color:#854d0e;">
                        Low: {{ $product->stock }}
                    </span>
                @else
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                          style="background-color:var(--color-status-active-bg);color:var(--color-status-active-text);">
                        Stok: {{ $product->stock }}
                    </span>
                @endif
                <span class="font-bold text-sm" style="color:var(--color-primary);">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    @endforeach

   

    <p id="no-results" class="hidden col-span-full text-center text-sm text-gray-400 py-10">
        No products match your search.
    </p>
</div>


{{-- Modal CSS (plain CSS — no Vite compilation needed) --}}
<style>
/* Force SweetAlert2 above all modals */
.swal2-container { z-index: 99999 !important; }

.pm-overlay   { position:fixed;inset:0;z-index:9000;display:none;align-items:center;
                justify-content:center;padding:16px;background:rgba(0,0,0,0.45);overflow-y:auto; }
.pm-overlay.pm-open { display:flex; }
.pm-panel     { position:relative;width:100%;max-width:920px;background:#fff;
                border-radius:40px;overflow:hidden;
                box-shadow:0px 25px 50px -12px rgba(0,0,0,0.25);margin:auto; }
.pm-close     { position:absolute;top:24px;right:32px;width:32px;height:32px;
                display:flex;align-items:center;justify-content:center;
                cursor:pointer;border:none;background:transparent;z-index:10;
                color:#9ca3af;opacity:.8; }
.pm-close:hover { opacity:1; }
.pm-header    { display:flex;align-items:center;gap:24px;padding:32px 40px 24px; }
.pm-thumb-wrap{ position:relative;flex-shrink:0; }
.pm-thumb     { width:112px;height:112px;border-radius:50%;background:#f1f5f9;
                border:1px solid #f3f4f6;overflow:hidden;
                display:flex;align-items:center;justify-content:center; }
.pm-thumb img { width:100%;height:100%;object-fit:cover; }
.pm-pencil    { position:absolute;bottom:4px;right:4px;width:30px;height:30px;
                border-radius:50%;background:#fff;border:1px solid #f3f4f6;
                box-shadow:0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -2px rgba(0,0,0,0.1);
                display:flex;align-items:center;justify-content:center;cursor:pointer; }
.pm-name-row  { flex:1;min-width:0;display:flex;align-items:center;gap:10px; }
.pm-name-input{ flex:1;font-size:26px;font-weight:700;color:#111827;
                border:none;border-bottom:2px solid #e5e7eb;background:transparent;
                outline:none;padding-bottom:4px;min-width:0; }
.pm-name-input:focus { border-bottom-color:#f87171; }
.pm-grid      { display:grid;grid-template-columns:1fr 1fr;gap:32px;padding:0 40px 8px; }
.pm-col       { display:flex;flex-direction:column;gap:18px; }
.pm-field     { display:flex;flex-direction:column;gap:7px; }
.pm-label-lg  { font-size:16px;font-weight:700;color:#1f2937;margin:0; }
.pm-label-sm  { font-size:13px;font-weight:700;color:#1f2937;margin:0; }
.pm-input     { width:100%;padding:14px 16px;border-radius:12px;
                border:1px solid #d1d5db;background:#fff;color:#111827;
                font-size:16px;outline:none;box-sizing:border-box; }
.pm-input:focus { border-color:#fca5a5;box-shadow:0 0 0 3px rgba(252,165,165,.3); }
.pm-select    { width:100%;padding:14px 40px 14px 16px;border-radius:12px;
                border:1px solid #d1d5db;background:#fff;color:#111827;
                font-size:16px;outline:none;appearance:none;box-sizing:border-box; }
.pm-select:focus { border-color:#fca5a5;box-shadow:0 0 0 3px rgba(252,165,165,.3); }
.pm-select-wrap { position:relative; }
.pm-select-wrap svg { position:absolute;right:12px;top:50%;transform:translateY(-50%);
                      pointer-events:none;width:18px;height:18px;color:#9ca3af; }
.pm-cat-row   { display:flex;gap:8px; }
.pm-cat-add   { width:48px;flex-shrink:0;border-radius:12px;border:none;
                background:var(--color-primary);color:#fff;
                font-size:22px;font-weight:700;cursor:pointer;
                display:flex;align-items:center;justify-content:center; }
.pm-textarea  { width:100%;padding:14px 16px;border-radius:12px;
                border:1px solid #d1d5db;background:#fff;color:#111827;
                font-size:16px;outline:none;resize:none;box-sizing:border-box; }
.pm-textarea:focus { border-color:#fca5a5;box-shadow:0 0 0 3px rgba(252,165,165,.3); }
.pm-stock-box { background:#fff;border:1px solid #e5e7eb;border-radius:16px;
                display:flex;flex-direction:column;gap:14px;padding:22px; }
.pm-stock-title { font-size:18px;font-weight:700;color:#111827;margin:0; }
.pm-alert-banner{ background:#fef2f2;border-radius:12px;padding:14px 16px;
                  display:flex;align-items:flex-start;gap:12px; }
.pm-alert-icon  { flex-shrink:0;background:#fff;border-radius:8px;padding:8px;
                  box-shadow:0 1px 1px rgba(0,0,0,.05); }
.pm-alert-title { font-size:13px;font-weight:700;color:#111827;margin:0 0 3px; }
.pm-alert-text  { font-size:11px;color:#6b7280;margin:0;line-height:1.5; }
.pm-pcs-wrap  { position:relative; }
.pm-pcs-wrap input { padding-right:48px; }
.pm-pcs-suffix{ position:absolute;right:14px;top:50%;transform:translateY(-50%);
                font-size:14px;color:#6b7280;pointer-events:none; }
.pm-hint      { font-size:11px;color:#9ca3af;margin:3px 0 0; }
.pm-status-section { display:flex;flex-direction:column;gap:10px; }
.pm-switcher  { display:flex;border-radius:16px;padding:6px;background:#f3f4f6; }
.pm-sw-btn    { flex:1;display:flex;align-items:center;justify-content:center;
                gap:8px;padding:11px;border-radius:12px;font-size:15px;font-weight:700;
                cursor:pointer;border:none;transition:all .15s;background:transparent; }
.pm-sw-dot    { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
.pm-footer    { display:flex;align-items:center;gap:16px;padding:20px 40px 32px; }
.pm-btn-cancel{ padding:14px 32px;border-radius:9999px;font-size:16px;font-weight:700;
                border:2px solid #111827;color:#111827;background:transparent;
                cursor:pointer;white-space:nowrap; }
.pm-btn-cancel:hover { opacity:.8; }
.pm-btn-submit{ flex:1;padding:14px;border-radius:9999px;font-size:16px;font-weight:700;
                border:none;background:#a81d1d;color:#fff;cursor:pointer; }
.pm-btn-submit:hover { opacity:.85; }
.pm-locked-msg{ font-size:11px;color:#ef4444;margin:4px 0 0;display:none; }
</style>

{{-- ADD PRODUCT MODAL --}}
<div id="modal-add" class="pm-overlay">
    <div class="pm-panel">
        <form id="form-add-product"
              action="{{ route('owner.products.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            {{-- Close --}}
            <button type="button" id="modal-close-btn" class="pm-close" title="Close">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Header: image + name --}}
            <div class="pm-header">
                <div class="pm-thumb-wrap">
                    <div class="pm-thumb">
                        <img id="add-img-preview" src="" alt="" style="display:none;">
                        <svg id="add-img-placeholder" width="52" height="52" fill="none"
                             viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <label for="add-input-image" class="pm-pencil" title="Upload image">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" viewBox="0 0 24 24" style="color:var(--color-primary);">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </label>
                    <input type="file" id="add-input-image" name="image"
                           style="display:none;" accept="image/*">
                </div>
                <div class="pm-name-row">
                    <input type="text" id="add-name" name="name"
                           class="pm-name-input" placeholder="Product Name" required>
                    <svg width="18" height="18" fill="none" stroke="#9ca3af" stroke-width="2"
                         stroke-linecap="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
            </div>

            {{-- 2-column grid --}}
            <div class="pm-grid">

                {{-- LEFT: pricing & details --}}
                <div class="pm-col">
                    <div class="pm-field">
                        <label class="pm-label-lg">Harga Modal</label>
                        <input type="number" id="add-cost" name="cost_price"
                               class="pm-input" min="0" step="1" placeholder="Value" required>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Harga Jual</label>
                        <input type="number" id="add-price" name="price"
                               class="pm-input" min="0" step="1" placeholder="Value" required>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Category</label>
                        <div class="pm-cat-row">
                            <div class="pm-select-wrap" style="flex:1;">
                                <select id="add-category" name="category_id" class="pm-select" required>
                                    <option value="">Select one Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <button type="button" id="btn-add-category"
                                    class="pm-cat-add btn-add-category" title="Add new category">+</button>
                        </div>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Description</label>
                        <textarea id="add-desc" name="description"
                                  class="pm-textarea" rows="6"
                                  placeholder="Enter a description..." required></textarea>
                    </div>
                </div>

                {{-- RIGHT: stock & status --}}
                <div class="pm-col">
                    {{-- Stock card --}}
                    <div class="pm-stock-box">
                        <p class="pm-stock-title">Add Stock</p>
                        <div class="pm-alert-banner">
                            <div class="pm-alert-icon">
                                <svg width="22" height="22" fill="none" stroke="#ef4444"
                                     stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                             a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                             1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="pm-alert-title">Kelola stok menu ini</p>
                                <p class="pm-alert-text">Tambahkan jumlah stok untuk memastikan menu selalu tersedia.</p>
                            </div>
                        </div>
                        <div class="pm-field">
                            <label class="pm-label-sm">Jumlah Stok</label>
                            <div class="pm-pcs-wrap">
                                <input type="number" id="add-stock" name="stock"
                                       class="pm-input" min="0" step="1" placeholder="Enter stock quantity" required>
                                <span class="pm-pcs-suffix">pcs</span>
                            </div>
                        </div>
                        <div class="pm-field">
                            <label class="pm-label-sm">Minimum Stok Alert</label>
                            <div class="pm-pcs-wrap">
                                <input type="number" name="min_stock_alert"
                                       class="pm-input" min="0" placeholder="Enter minimum stock">
                                <span class="pm-pcs-suffix">pcs</span>
                            </div>
                            <p class="pm-hint">Notifikasi ketika stok mencapai batas minimum</p>
                        </div>
                    </div>

                    {{-- Status Menu --}}
                    <div class="pm-status-section">
                        <label class="pm-label-lg">Status Menu</label>
                        <div class="pm-switcher">
                            <button type="button" id="add-btn-available" class="pm-sw-btn"
                                    style="background:#fff;color:#22c55e;
                                           box-shadow:0 1px 1.5px rgba(0,0,0,.1),0 1px 1px rgba(0,0,0,.06);">
                                <span class="pm-sw-dot" style="background:#22c55e;"></span>
                                Available
                            </button>
                            <button type="button" id="add-btn-unavailable" class="pm-sw-btn"
                                    style="color:#9ca3af;">
                                <span class="pm-sw-dot" style="background:#9ca3af;"></span>
                                Unavailable
                            </button>
                        </div>
                        <input type="hidden" id="add-is-available" name="is_available" value="1">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-footer">
                <button type="button" id="modal-cancel-btn" class="pm-btn-cancel">Cancel</button>
                <button type="submit" class="pm-btn-submit">Add Product</button>
            </div>
        </form>
    </div>
</div>


{{-- EDIT PRODUCT MODAL --}}
<div id="modal-edit" class="pm-overlay">
    <div class="pm-panel">
        <form id="form-edit-product" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Close --}}
            <button type="button" id="edit-close-btn" class="pm-close" title="Close">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Header: image + name --}}
            <div class="pm-header">
                <div class="pm-thumb-wrap">
                    <div class="pm-thumb">
                        <img id="edit-img-preview" src="" alt="">
                    </div>
                    <label for="edit-input-image" class="pm-pencil" title="Change image">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" viewBox="0 0 24 24" style="color:var(--color-primary);">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </label>
                    <input type="file" id="edit-input-image" name="image"
                           style="display:none;" accept="image/*">
                </div>
                <div class="pm-name-row">
                    <input type="text" id="edit-name" name="name"
                           class="pm-name-input" placeholder="Product Name" required>
                    <svg width="18" height="18" fill="none" stroke="#9ca3af" stroke-width="2"
                         stroke-linecap="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
            </div>

            {{-- 2-column grid --}}
            <div class="pm-grid">

                {{-- LEFT --}}
                <div class="pm-col">
                    <div class="pm-field">
                        <label class="pm-label-lg">Harga Modal</label>
                        <input type="number" id="edit-cost" name="cost_price"
                               class="pm-input" min="0" step="1" placeholder="Value" required>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Harga Jual</label>
                        <input type="number" id="edit-price" name="price"
                               class="pm-input" min="0" step="1" placeholder="Value" required>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Category</label>
                        <div class="pm-cat-row">
                            <div class="pm-select-wrap" style="flex:1;">
                                <select id="edit-category" name="category_id" class="pm-select" required>
                                    <option value="">Select one Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <button type="button" class="pm-cat-add btn-add-category"
                                    title="Add new category">+</button>
                        </div>
                    </div>
                    <div class="pm-field">
                        <label class="pm-label-lg">Description</label>
                        <textarea id="edit-desc" name="description"
                                  class="pm-textarea" rows="6"
                                  placeholder="Enter a description..." required></textarea>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="pm-col">
                    {{-- Stock card --}}
                    <div class="pm-stock-box">
                        <p class="pm-stock-title">Add Stock</p>
                        <div class="pm-alert-banner">
                            <div class="pm-alert-icon">
                                <svg width="22" height="22" fill="none" stroke="#ef4444"
                                     stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                             a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                             1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="pm-alert-title">Kelola stok menu ini</p>
                                <p class="pm-alert-text">Tambahkan jumlah stok untuk memastikan menu selalu tersedia.</p>
                            </div>
                        </div>
                        <div class="pm-field">
                            <label class="pm-label-sm">Jumlah Stok</label>
                            <div class="pm-pcs-wrap">
                                <input type="number" id="edit-stock" name="stock"
                                       class="pm-input" min="0" step="1" placeholder="Enter stock quantity" required>
                                <span class="pm-pcs-suffix">pcs</span>
                            </div>
                        </div>
                        <div class="pm-field">
                            <label class="pm-label-sm">Minimum Stok Alert</label>
                            <div class="pm-pcs-wrap">
                                <input type="number" id="edit-min-stock" name="min_stock_alert"
                                       class="pm-input" min="0" placeholder="Enter minimum stock">
                                <span class="pm-pcs-suffix">pcs</span>
                            </div>
                            <p class="pm-hint">Notifikasi ketika stok mencapai batas minimum</p>
                        </div>
                    </div>

                    {{-- Status Menu --}}
                    <div class="pm-status-section">
                        <label class="pm-label-lg">Status Menu</label>
                        <div class="pm-switcher">
                            <button type="button" id="edit-btn-available" class="pm-sw-btn"
                                    style="background:#fff;color:#22c55e;
                                           box-shadow:0 1px 1.5px rgba(0,0,0,.1),0 1px 1px rgba(0,0,0,.06);">
                                <span class="pm-sw-dot" style="background:#22c55e;"></span>
                                Available
                            </button>
                            <button type="button" id="edit-btn-unavailable" class="pm-sw-btn"
                                    style="color:#9ca3af;">
                                <span class="pm-sw-dot" style="background:#9ca3af;"></span>
                                Unavailable
                            </button>
                        </div>
                        <input type="hidden" id="edit-is-available" name="is_available" value="1">
                        <p id="edit-status-locked-msg" class="pm-locked-msg">
                            Status otomatis Unavailable karena stok = 0.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-footer">
                <button type="button" id="edit-cancel-btn" class="pm-btn-cancel">Cancel</button>
                <button type="submit" class="pm-btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden delete form --}}
<form id="form-delete-product" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
$(function () {

    /* Attach CSRF token to every AJAX request automatically */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /* 
       SHARED HELPERS
     */

    /* Status switcher: sets active/inactive styles + hidden input + auto-lock on zero stock */
    function setStatus(prefix, available, locked) {
        const $btnAvail  = $('#' + prefix + '-btn-available');
        const $btnUnavail= $('#' + prefix + '-btn-unavailable');
        const $hidden    = $('#' + prefix + '-is-available');

        if (available) {
            $btnAvail.css({ background:'#fff', color:'#22c55e',
                            boxShadow:'0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnUnavail.css({ background:'transparent', color:'#9ca3af', boxShadow:'none' });
            $hidden.val('1');
        } else {
            $btnAvail.css({ background:'transparent', color:'#9ca3af', boxShadow:'none' });
            $btnUnavail.css({ background:'#fff', color:'#ef4444',
                              boxShadow:'0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnUnavail.find('span').css('background','#ef4444');
            $hidden.val('0');
        }

        /* Lock / unlock buttons when stock is 0 */
        if (locked) {
            $btnAvail.prop('disabled', true).css('opacity','0.4');
            $btnUnavail.prop('disabled', true);
            if (prefix === 'edit') $('#edit-status-locked-msg').css('display','block');
        } else {
            $btnAvail.prop('disabled', false).css('opacity','1');
            $btnUnavail.prop('disabled', false);
            if (prefix === 'edit') $('#edit-status-locked-msg').css('display','none');
        }
    }

    /* 
       ADD MODAL
     */
    function openAddModal() {
        $('#form-add-product')[0].reset();
        $('#add-img-preview').css('display','none');
        $('#add-img-placeholder').css('display','block');
        setStatus('add', true, false);
        $('#modal-add').addClass('pm-open');
        $('body').css('overflow','hidden');
    }
    function closeAddModal() {
        $('#modal-add').removeClass('pm-open');
        $('body').css('overflow','');
    }

    $('#btn-add-product').on('click', openAddModal);
    $('#modal-close-btn, #modal-cancel-btn').on('click', closeAddModal);
    $('#modal-add').on('click', function (e) { if ($(e.target).is('#modal-add')) closeAddModal(); });

    /* Image preview */
    $('#add-input-image').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#add-img-preview').attr('src', e.target.result).css('display','block');
            $('#add-img-placeholder').css('display','none');
        };
        reader.readAsDataURL(file);
    });

    /* Status switcher clicks */
    $('#add-btn-available').on('click', function () { setStatus('add', true, false); });
    $('#add-btn-unavailable').on('click', function () { setStatus('add', false, false); });

    /* Auto-rule: stock input → lock/unlock + auto-Available when stock ≥ 1 */
    $('#add-stock').on('input change', function () {
        const stock       = parseInt($(this).val(), 10) || 0;
        const $btnAvail   = $('#add-btn-available');
        const $btnUnavail = $('#add-btn-unavailable');
        const $hidden     = $('#add-is-available');

        if (stock <= 0) {
            $hidden.val('0');
            $btnAvail.prop('disabled', true).css({ opacity: '0.4', cursor: 'not-allowed', color: '#9ca3af' });
            $btnAvail.find('.pm-sw-dot').css('background', '#9ca3af');
            $btnUnavail.prop('disabled', true).css({ opacity: '1', cursor: 'not-allowed', color: '#ef4444',
                                                     background: '#fff',
                                                     boxShadow: '0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnUnavail.find('.pm-sw-dot').css('background', '#ef4444');
        } else {
            $hidden.val('1');
            $btnAvail.prop('disabled', false).css({ opacity: '1', cursor: 'pointer', color: '#22c55e',
                                                    background: '#fff',
                                                    boxShadow: '0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnAvail.find('.pm-sw-dot').css('background', '#22c55e');
            $btnUnavail.prop('disabled', false).css({ opacity: '1', cursor: 'pointer', color: '#9ca3af',
                                                      background: 'transparent', boxShadow: 'none' });
            $btnUnavail.find('.pm-sw-dot').css('background', '#9ca3af');
        }
    });

    /*  On-the-fly category creation  */
    $(document).on('click', '#btn-add-category, .btn-add-category', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Tambah Kategori Baru',
            input: 'text',
            inputPlaceholder: 'Misal: Toppoki, Minuman, dll...',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#a81d1d',
            showLoaderOnConfirm: true,
            preConfirm: function (categoryName) {
                if (!categoryName || !categoryName.trim()) {
                    Swal.showValidationMessage('Nama kategori tidak boleh kosong!');
                    return false;
                }
                return $.ajax({
                    url: '{{ route("owner.category.storeAjax") }}',
                    type: 'POST',
                    data: { name: categoryName.trim() }
                }).then(function (response) {
                    return response;
                }).catch(function (error) {
                    const msg = error.responseJSON?.errors?.name?.[0]
                              ?? error.responseJSON?.message
                              ?? 'Server error';
                    Swal.showValidationMessage('Gagal menyimpan: ' + msg);
                });
            },
            allowOutsideClick: function () { return !Swal.isLoading(); }
        }).then(function (result) {
            if (result.isConfirmed && result.value && result.value.id) {
                const newId   = result.value.id;
                const newName = result.value.name;

                /* Append to both dropdowns; auto-select in the Add modal */
                $('#add-category').append(new Option(newName, newId, true, true));
                $('#edit-category').append(new Option(newName, newId, false, false));

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori "' + newName + '" telah ditambahkan.',
                    confirmButtonColor: '#a81d1d',
                    timer: 1500,
                    showConfirmButton: false,
                });
            }
        });
    });

    /* 
       EDIT MODAL
     */
    function closeEditModal() {
        $('#modal-edit').removeClass('pm-open');
        $('body').css('overflow','');
    }
    $('#edit-close-btn, #edit-cancel-btn').on('click', closeEditModal);
    $('#modal-edit').on('click', function (e) { if ($(e.target).is('#modal-edit')) closeEditModal(); });

    /* Image preview */
    $('#edit-input-image').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) { $('#edit-img-preview').attr('src', e.target.result); };
        reader.readAsDataURL(file);
    });

    /* Status switcher clicks */
    $('#edit-btn-available').on('click', function () {
        if ($(this).prop('disabled')) return;
        setStatus('edit', true, false);
    });
    $('#edit-btn-unavailable').on('click', function () {
        if ($(this).prop('disabled')) return;
        setStatus('edit', false, false);
    });

    /* Auto-rule: stock → lock/unlock + auto-Available when stock ≥ 1 */
    $('#edit-stock').on('input change', function () {
        let currentStock = parseInt($(this).val()) || 0;
        let $btnAvail    = $('#edit-btn-available');
        let $btnUnavail  = $('#edit-btn-unavailable');
        let $lockedMsg   = $('#edit-status-locked-msg');
        let $hiddenInput = $('#edit-is-available');

        if (currentStock <= 0) {
            $hiddenInput.val('0');
            $btnAvail.prop('disabled', true).css({ opacity: '0.4', cursor: 'not-allowed', color: '#9ca3af',
                                                   background: 'transparent', boxShadow: 'none' });
            $btnAvail.find('.pm-sw-dot').css('background', '#9ca3af');
            $btnUnavail.prop('disabled', true).css({ opacity: '1', cursor: 'not-allowed', color: '#ef4444',
                                                     background: '#fff',
                                                     boxShadow: '0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnUnavail.find('.pm-sw-dot').css('background', '#ef4444');
            $lockedMsg.css('display', 'block');
        } else {
            /* Always snap to Available when stock goes from 0 → ≥ 1 */
            $hiddenInput.val('1');
            $btnAvail.prop('disabled', false).css({ opacity: '1', cursor: 'pointer', color: '#22c55e',
                                                    background: '#fff',
                                                    boxShadow: '0px 1px 1.5px rgba(0,0,0,0.1),0px 1px 1px rgba(0,0,0,0.06)' });
            $btnAvail.find('.pm-sw-dot').css('background', '#22c55e');
            $btnUnavail.prop('disabled', false).css({ opacity: '1', cursor: 'pointer', color: '#9ca3af',
                                                      background: 'transparent', boxShadow: 'none' });
            $btnUnavail.find('.pm-sw-dot').css('background', '#9ca3af');
            $lockedMsg.css('display', 'none');
        }
    });

    /* Open edit modal — populate fields from data-* */
    $(document).on('click', '.btn-edit', function () {
        const btn       = $(this);
        const id        = btn.data('id');
        const stockVal  = parseInt(btn.data('stock'), 10) || 0;
        const available = btn.data('available') === '1' || btn.data('available') === 1;
        const locked    = stockVal <= 0;

        $('#form-edit-product').attr('action', '/owner/products/' + id);
        $('#edit-name').val(btn.data('name'));
        $('#edit-category').val(btn.data('category-id'));
        $('#edit-price').val(btn.data('price'));
        $('#edit-cost').val(btn.data('cost'));
        $('#edit-stock').val(stockVal);
        $('#edit-min-stock').val(btn.data('low-stock') || '');
        $('#edit-desc').val(btn.data('desc') || '');

        const img = btn.data('image') || '{{ asset("assets/img/CA_ORIGINAL.png") }}';
        $('#edit-img-preview').attr('src', img);

        setStatus('edit', available && !locked, locked);

        $('#modal-edit').addClass('pm-open');
        $('body').css('overflow','hidden');
    });

    /* 
       DELETE
     */
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Delete "' + name + '"?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#a81d1d',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                const form = $('#form-delete-product');
                form.attr('action', '/owner/products/' + id);
                form.submit();
            }
        });
    });

    /* 
       ESC key closes any open modal
     */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') { closeAddModal(); closeEditModal(); }
    });

    /* 
       SEARCH + CATEGORY FILTER
     */
    function applyFilter() {
        const q   = $('#search-products').val().toLowerCase().trim();
        const cat = $('#filter-category').val();
        let vis   = 0;

        $('.product-card').each(function () {
            const name    = $(this).data('name') || '';
            const cardCat = $(this).data('category') || '';
            const show    = (!q || name.includes(q)) && (!cat || cardCat === cat);
            $(this).toggle(show);
            if (show) vis++;
        });

        $('#no-results').toggle(vis === 0);
    }

    $('#search-products').on('input', applyFilter);
    $('#filter-category').on('change', applyFilter);

});
</script>
@endpush
