@extends('layouts.app')

@section('title', 'Kasir — Purchase')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{ config('services.midtrans.is_production')
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key', '') }}"></script>

{{-- ══════════════════════════════════════════════════════════════
     CASHIER POS
     Left:  Product catalog — AJAX-paginated grid
     Right: Order panel — sticky, internally scrollable cart
══════════════════════════════════════════════════════════════ --}}

<div class="flex items-center gap-3 mb-2">
    <h1 class="text-xl font-black tracking-tight leading-none" style="color:var(--color-black);">Kasir</h1>
    <span id="store-status-badge"
          style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                 border-radius:9999px;font-size:11px;font-weight:700;
                 {{ $storeInfo['is_open'] ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;' }}">
        <span id="store-status-dot"
              style="width:7px;height:7px;border-radius:50%;flex-shrink:0;
                     background:{{ $storeInfo['is_open'] ? '#22c55e' : '#ef4444' }};"></span>
        <span id="store-status-text">{{ $storeInfo['is_open'] ? 'Toko Buka' : 'Toko Tutup' }}</span>
    </span>
</div>
{{-- lg:items-start is CRITICAL — without it flex children stretch to equal height
     which defeats position:sticky on the right column --}}
<div class="flex flex-col lg:flex-row gap-5 lg:items-start">

    {{-- ══════════════════════════════════════════
         LEFT — Product Catalog
    ══════════════════════════════════════════ --}}
    <div class="w-full lg:w-7/12 xl:w-8/12 flex flex-col gap-4">

        {{-- Search --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                 style="color:#9CA3AF;"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input type="search" id="product-search" placeholder="Cari produk…"
                   autocomplete="off"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm focus:outline-none"
                   style="border:1px solid var(--color-border);background:#fff;">
        </div>

        {{-- Category pills (server-rendered from DB; Semua always first) --}}
        <div class="flex gap-2 flex-wrap" id="cat-pills">
            <button class="cat-pill active px-4 py-1.5 rounded-full text-xs font-bold transition-colors"
                    data-cat="all"
                    style="background:var(--color-primary);color:#fff;border:1.5px solid var(--color-primary);">
                Semua
            </button>
            @foreach ($categories as $cat)
                <button class="cat-pill px-4 py-1.5 rounded-full text-xs font-bold border transition-colors"
                        data-cat="{{ $cat->name }}"
                        style="border-color:var(--color-border);color:#555;background:#fff;">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        {{-- Product grid — populated by loadProducts() on page load --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3" id="product-grid">
            {{-- Loading skeleton --}}
            @for ($i = 0; $i < 8; $i++)
                <div class="rounded-2xl p-3 flex flex-col items-center gap-2 animate-pulse"
                     style="background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                    <div class="w-20 h-20 rounded-full" style="background:#F3F4F6;"></div>
                    <div class="h-3 w-3/4 rounded" style="background:#F3F4F6;"></div>
                    <div class="h-3 w-1/2 rounded" style="background:#F3F4F6;"></div>
                </div>
            @endfor
        </div>

        {{-- Pagination --}}
        <div id="product-pagination" class="flex flex-wrap justify-center gap-2 pb-6"></div>

    </div>{{-- /left --}}

    {{-- ══════════════════════════════════════════
         RIGHT — Order Panel (sticky, strict viewport height)
         sticky top-4 + explicit height keeps the Order button pinned.
         flex flex-col + shrink-0 sections + flex-1 cart = no page scroll needed.
    ══════════════════════════════════════════ --}}
    <div class="w-full lg:w-5/12 xl:w-4/12 sticky top-4 h-[calc(100vh-2rem)] flex flex-col bg-white rounded-2xl shadow overflow-hidden">

        {{-- ── Panel header (flex-none) ──────────────────────────── --}}
        <div class="flex-none border-b border-gray-100 p-3">
            <h2 class="font-black text-sm" style="color:var(--color-black);">Order Menu</h2>
        </div>

        {{-- ── Customer Info Box (flex-none — never scrolls) ── --}}
        <div class="flex-none border-b border-gray-100 p-3">
                <div id="customer-box"
                     style="border-radius:8px;padding:8px;
                            border:1.5px solid #FFBE54;background:#FFFBF2;">

                    {{-- STATE 1: Input form --}}
                    <div id="customer-form-state">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-bold" style="color:var(--color-black);">Info Pelanggan</p>
                            <span class="text-[10px] font-semibold flex items-center gap-1"
                                  style="color:#25D366;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:10px;height:10px;"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                No. dipakai untuk struk WA
                            </span>
                        </div>

                        {{-- Phone input + dropdown --}}
                        <div style="position:relative;margin-bottom:4px;" id="phone-wrap">
                            <input type="text" id="customer-phone-input"
                                   placeholder="No. WhatsApp (opsional)…"
                                   autocomplete="off"
                                   inputmode="numeric"
                                   class="w-full text-sm rounded-lg px-3 py-1.5 focus:outline-none"
                                   style="border:1px solid #FFD080;background:#fff;">
                            <div id="phone-dropdown"
                                 style="display:none;position:absolute;left:0;right:0;
                                        top:calc(100% + 4px);background:#fff;
                                        border:1px solid #F0F0F0;border-radius:10px;
                                        box-shadow:0 4px 20px rgba(0,0,0,0.10);
                                        z-index:300;overflow:hidden;max-height:180px;overflow-y:auto;">
                            </div>
                        </div>

                        {{-- Name input + dropdown --}}
                        <div style="position:relative;margin-bottom:5px;" id="name-wrap">
                            <input type="text" id="customer-name-input"
                                   placeholder="Nama pelanggan…"
                                   autocomplete="off"
                                   class="w-full text-sm rounded-lg px-3 py-1.5 focus:outline-none"
                                   style="border:1px solid #FFD080;background:#fff;">
                            <div id="name-dropdown"
                                 style="display:none;position:absolute;left:0;right:0;
                                        top:calc(100% + 4px);background:#fff;
                                        border:1px solid #F0F0F0;border-radius:10px;
                                        box-shadow:0 4px 20px rgba(0,0,0,0.10);
                                        z-index:300;overflow:hidden;max-height:180px;overflow-y:auto;">
                            </div>
                        </div>

                        {{-- Order type --}}
                        <div class="flex gap-1.5 mb-1.5">
                            <label class="flex-1">
                                <input type="radio" name="order_type" value="dine-in"
                                       class="sr-only order-type-radio">
                                <div class="order-type-label text-xs font-bold text-center py-1.5
                                            rounded-lg cursor-pointer transition-colors"
                                     style="border:1.5px solid #E5E7EB;color:#9CA3AF;background:#fff;">
                                    Dine-in
                                </div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="order_type" value="takeaway"
                                       class="sr-only order-type-radio" checked>
                                <div class="order-type-label text-xs font-bold text-center py-1.5
                                            rounded-lg cursor-pointer transition-colors"
                                     style="border:1.5px solid var(--color-primary);
                                            color:var(--color-primary);background:#FFF5F5;">
                                    Takeaway
                                </div>
                            </label>
                        </div>

                        <button id="btn-save-customer" type="button"
                                class="w-full text-xs font-bold py-2 rounded-lg"
                                style="background:var(--color-primary);color:#fff;">
                            Simpan Info Pelanggan
                        </button>
                    </div>{{-- /form state --}}

                    {{-- STATE 2: Saved card --}}
                    <div id="customer-saved-state" style="display:none;">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;
                                            background:var(--color-primary);display:flex;
                                            align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         style="width:18px;height:18px;"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke="#fff" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0z
                                                 M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p id="saved-customer-name"
                                       class="text-sm font-bold" style="color:var(--color-black);margin:0;">—</p>
                                    <p id="saved-customer-phone"
                                       class="text-xs" style="color:#9CA3AF;margin:0;"></p>
                                    <p id="saved-order-type"
                                       class="text-xs" style="color:#9CA3AF;margin:0;">Takeaway</p>
                                </div>
                            </div>
                            <button id="btn-edit-customer" type="button"
                                    class="text-xs font-bold px-3 py-1.5 rounded-lg"
                                    style="border:1.5px solid var(--color-primary);
                                           color:var(--color-primary);background:transparent;">
                                Edit
                            </button>
                        </div>
                    </div>{{-- /saved state --}}

                </div>
        </div>{{-- /customer box --}}

        {{-- ── Cart list (flex-1 overflow-y-auto) — ONLY this section scrolls ── --}}
        <div id="cart-wrapper" class="flex-1 overflow-y-auto p-3">

                {{-- Empty state --}}
                <div id="cart-empty"
                     style="display:flex;flex-direction:column;align-items:center;
                            justify-content:center;text-align:center;padding:24px 16px;
                            height:100%;">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         style="width:48px;height:48px;color:#E5E7EB;margin-bottom:10px;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25
                                 a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684
                                 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106
                                 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75
                                 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <p class="text-sm font-bold" style="color:#9CA3AF;margin:0 0 4px;">
                        Belum ada item
                    </p>
                    <p class="text-xs" style="color:#C4C4C4;margin:0;">
                        Simpan info pelanggan lalu pilih menu
                    </p>
                </div>

                {{-- Item rows injected here by renderCart() --}}

            </div>{{-- /cart-wrapper --}}

        {{-- ── Footer — totals + Order button (flex-none, always pinned) ── --}}
        <div class="flex-none p-3 border-t border-gray-100 bg-white">

            <div id="cart-summary" style="display:none;margin-bottom:8px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:11px;color:#9CA3AF;">Subtotal</span>
                    <span id="pos-subtotal" style="font-size:11px;font-weight:600;color:#555;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:11px;color:#9CA3AF;">Pajak (11%)</span>
                    <span id="pos-tax" style="font-size:11px;font-weight:600;color:#555;">Rp 0</span>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
                <div style="flex-shrink:0;">
                    <p style="font-size:10px;color:#9CA3AF;margin:0 0 1px;">Total</p>
                    <p id="pos-total"
                       style="font-size:20px;font-weight:900;color:var(--color-primary);margin:0;line-height:1.1;">
                        Rp 0
                    </p>
                </div>
                <button id="btn-order" type="button"
                        class="flex-1 font-black text-sm py-3 rounded-xl"
                        style="background:var(--color-primary);color:#fff;
                               opacity:0.4;cursor:not-allowed;transition:opacity .2s;">
                    Order
                </button>
            </div>

        </div>

    </div>{{-- /right --}}

</div>{{-- /flex --}}


{{-- ══════════════════════════════════════════════════════════════
     PAYMENT MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="payment-modal"
     style="display:none;position:fixed;inset:0;z-index:8000;
            background:rgba(0,0,0,0.45);
            align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:400px;
                box-shadow:0 8px 40px rgba(0,0,0,0.18);overflow:hidden;">

        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:16px 20px;border-bottom:1px solid #F0F0F0;">
            <h3 style="font-size:15px;font-weight:800;color:var(--color-black);margin:0;">
                Konfirmasi Pembayaran
            </h3>
            <button id="btn-close-payment" type="button"
                    style="width:34px;height:34px;border-radius:50%;border:none;
                           background:transparent;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;color:#9CA3AF;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div style="padding:20px;">
            <p style="font-size:11px;font-weight:700;color:#9CA3AF;letter-spacing:.06em;
                       text-transform:uppercase;margin:0 0 10px;">
                Metode Pembayaran
            </p>
            <div class="flex gap-2 mb-5">
                @foreach (['Cash', 'QRIS', 'Debit'] as $pm)
                    <label class="flex-1">
                        <input type="radio" name="payment_method" value="{{ $pm }}"
                               class="sr-only payment-radio"
                               @if ($pm === 'Cash') checked @endif>
                        <div class="payment-method-label text-xs font-bold text-center py-2.5 rounded-xl cursor-pointer"
                             style="border:1.5px solid {{ $pm === 'Cash' ? 'var(--color-primary)' : '#E5E7EB' }};
                                    color:{{ $pm === 'Cash' ? 'var(--color-primary)' : '#9CA3AF' }};
                                    background:{{ $pm === 'Cash' ? '#FFF5F5' : '#fff' }};">
                            {{ $pm }}
                        </div>
                    </label>
                @endforeach
            </div>

            <div style="background:#F9FAFB;border-radius:12px;padding:14px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12px;color:#9CA3AF;">Pelanggan</span>
                    <span id="modal-customer" style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12px;color:#9CA3AF;">Tipe Order</span>
                    <span id="modal-order-type" style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12px;color:#9CA3AF;">Subtotal</span>
                    <span id="modal-subtotal" style="font-size:12px;font-weight:600;color:#555;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;
                            padding-bottom:10px;border-bottom:1px dashed #E5E7EB;margin-bottom:8px;">
                    <span style="font-size:12px;color:#9CA3AF;">Pajak (11%)</span>
                    <span id="modal-tax" style="font-size:12px;font-weight:600;color:#555;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:14px;font-weight:700;color:var(--color-black);">Total</span>
                    <span id="modal-total" style="font-size:18px;font-weight:900;color:var(--color-primary);">Rp 0</span>
                </div>
            </div>

            <button id="btn-confirm-order" type="button"
                    class="w-full font-black text-sm py-3.5 rounded-xl"
                    style="background:var(--color-primary);color:#fff;">
                Konfirmasi &amp; Proses
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PROCESSING MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="processing-modal"
     style="display:none;position:fixed;inset:0;z-index:8100;
            background:rgba(0,0,0,0.55);
            align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:320px;
                box-shadow:0 8px 40px rgba(0,0,0,0.18);padding:40px 24px;text-align:center;">
        <div style="width:64px;height:64px;margin:0 auto 20px;
                    border:5px solid #F0F0F0;border-top-color:var(--color-primary);
                    border-radius:50%;animation:pospin .8s linear infinite;"></div>
        <p style="font-size:16px;font-weight:800;color:var(--color-black);margin:0 0 6px;">
            Making Receipt
        </p>
        <p style="font-size:13px;color:#9CA3AF;margin:0;">
            Processing your order<br>Please wait a moment…
        </p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SUCCESS / RECEIPT MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="success-modal"
     style="display:none;position:fixed;inset:0;z-index:8200;
            background:rgba(0,0,0,0.50);
            align-items:center;justify-content:center;padding:16px;overflow-y:auto;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:380px;
                box-shadow:0 8px 40px rgba(0,0,0,0.18);overflow:hidden;margin:auto;">

        <div style="background:#22C55E;padding:28px 24px 22px;text-align:center;">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.25);
                        display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;"
                     fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p style="font-size:20px;font-weight:900;color:#fff;margin:0 0 4px;">Order Complete!</p>
            <p style="font-size:12px;color:rgba(255,255,255,0.80);margin:0;">Order berhasil dibuat</p>
        </div>

        <div style="padding:20px;">
            <div style="background:#F9FAFB;border-radius:12px;padding:14px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:#9CA3AF;">Order #</span>
                    <span id="receipt-order-no"
                          style="font-size:12px;font-weight:800;font-family:monospace;color:var(--color-black);">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:#9CA3AF;">Pelanggan</span>
                    <span id="receipt-customer"
                          style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
                <div id="receipt-phone-row"
                     style="display:none;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:#9CA3AF;">Telepon</span>
                    <span id="receipt-phone"
                          style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:#9CA3AF;">Tipe Order</span>
                    <span id="receipt-order-type"
                          style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:12px;color:#9CA3AF;">Pembayaran</span>
                    <span id="receipt-payment"
                          style="font-size:12px;font-weight:700;color:var(--color-black);">—</span>
                </div>
            </div>

            <div id="receipt-items"
                 style="background:#F9FAFB;border-radius:12px;padding:14px;margin-bottom:12px;"></div>

            <div style="border-top:1.5px dashed #E5E7EB;padding-top:12px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12px;color:#9CA3AF;">Subtotal</span>
                    <span id="receipt-subtotal" style="font-size:12px;font-weight:600;color:#555;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:12px;color:#9CA3AF;">Pajak (11%)</span>
                    <span id="receipt-tax" style="font-size:12px;font-weight:600;color:#555;">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:15px;font-weight:800;color:var(--color-black);">Total</span>
                    <span id="receipt-total"
                          style="font-size:20px;font-weight:900;color:var(--color-primary);">Rp 0</span>
                </div>
            </div>

            {{-- WhatsApp button — only visible when customer has a phone number --}}
            <button id="btn-send-wa" type="button"
                    class="w-full font-bold text-sm py-3 rounded-xl mb-2 hidden"
                    style="background:#25D366;color:#fff;display:none;">
                <span id="btn-send-wa-label" class="flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;flex-shrink:0;"
                         viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Kirim Struk via WhatsApp
                </span>
                <span id="btn-send-wa-loading" style="display:none;">Mengirim…</span>
            </button>

            <button id="btn-new-order" type="button"
                    class="w-full font-bold text-sm py-3 rounded-xl mb-2"
                    style="background:var(--color-primary);color:#fff;">
                Order Baru
            </button>
            <button id="btn-close-success" type="button"
                    class="w-full font-bold text-sm py-3 rounded-xl"
                    style="background:#F3F4F6;color:#555;">
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
@keyframes pospin { to { transform: rotate(360deg); } }
</style>

@endsection

@push('scripts')
<script>
$(function () {

    /* ── CSRF for all AJAX ─────────────────────────────────────── */
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    /* ══════════════════════════════════════════════════════════════
       STATE
    ══════════════════════════════════════════════════════════════ */
    let cart          = {};   // { id: { id, name, price, qty, stock, img } }
    let customerSaved = false;
    let customerId    = null;
    let customerName  = '';
    let customerPhone = '';
    let orderType     = 'takeaway';
    let lastOrderId   = null;  // stored after successful POS submission
    let storeIsOpen   = {{ $storeInfo['is_open'] ? 'true' : 'false' }};

    let productTimer  = null;   // debounce for product search
    let phoneTimer    = null;   // debounce for phone search
    let nameTimer     = null;   // debounce for name search
    let activeCat     = 'all';

    const fmtRp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ══════════════════════════════════════════════════════════════
       PRODUCT GRID — AJAX pagination
    ══════════════════════════════════════════════════════════════ */
    function buildProductCard(p) {
        const img        = p.image || '{{ asset("assets/img/CA_ORIGINAL.png") }}';
        const outOfStock = !p.is_available || p.stock <= 0;

        const disabledStyles = outOfStock
            ? 'opacity:0.55;cursor:not-allowed;pointer-events:none;'
            : 'cursor:pointer;';

        const badge = outOfStock
            ? `<span style="display:inline-block;margin-top:3px;font-size:9px;font-weight:700;
                            padding:1px 7px;border-radius:9999px;
                            background:#fee2e2;color:#b91c1c;">Habis</span>`
            : `<p class="text-xs font-black" style="color:var(--color-primary);margin-top:2px;">
                   ${fmtRp(p.price)}
               </p>`;

        return `<div class="product-card flex flex-col items-center text-center rounded-2xl p-3
                            transition-all duration-150 ${outOfStock ? '' : 'hover:-translate-y-0.5'}"
                     style="background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);${disabledStyles}"
                     data-id="${p.id}"
                     data-name="${escHtml(p.name)}"
                     data-price="${p.price}"
                     data-stock="${p.stock}"
                     data-available="${p.is_available ? '1' : '0'}"
                     data-category="${escHtml(p.category || '')}"
                     data-img="${escHtml(img)}">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-2 flex-none"
                         style="background:#FDECD8;">
                        <img src="${escHtml(img)}" alt="${escHtml(p.name)}"
                             class="w-16 h-16 object-contain"
                             onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                    </div>
                    <p class="text-xs font-bold leading-snug" style="color:var(--color-black);">
                        ${escHtml(p.name)}
                    </p>
                    ${badge}
                </div>`;
    }

    function buildProductPagination(current, last) {
        const $pg = $('#product-pagination').empty();
        if (last <= 1) return;

        const btn = (page, label, active) =>
            `<button class="prod-page-btn text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                     data-page="${page}"
                     style="${active
                         ? 'background:var(--color-primary);color:#fff;'
                         : 'background:#F3F4F6;color:#555;'}">
                 ${label}
             </button>`;

        let html = btn(Math.max(1, current - 1), '←', false);
        for (let i = 1; i <= last; i++) {
            if (i === 1 || i === last || Math.abs(i - current) <= 2) {
                html += btn(i, i, i === current);
            } else if (Math.abs(i - current) === 3) {
                html += `<span style="line-height:1;padding:0 4px;color:#9CA3AF;font-size:12px;">…</span>`;
            }
        }
        html += btn(Math.min(last, current + 1), '→', false);
        $pg.html(html);
    }

    function loadProducts(page) {
        page = page || 1;
        const search = $('#product-search').val().trim();

        /* Skeleton while loading */
        let skeletons = '';
        for (let i = 0; i < 8; i++) {
            skeletons += `<div class="rounded-2xl p-3 flex flex-col items-center gap-2 animate-pulse"
                               style="background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                              <div class="w-20 h-20 rounded-full" style="background:#F3F4F6;"></div>
                              <div class="h-3 w-3/4 rounded" style="background:#F3F4F6;"></div>
                              <div class="h-3 w-1/2 rounded" style="background:#F3F4F6;"></div>
                          </div>`;
        }
        $('#product-grid').html(skeletons);
        $('#product-pagination').empty();

        $.get('{{ route("cashier.get-products") }}', {
            category : activeCat,
            search   : search,
            page     : page,
        }, function (res) {
            const $grid = $('#product-grid').empty();

            if (!res.products || !res.products.length) {
                $grid.html(`<div class="col-span-full text-center py-10 text-sm" style="color:#9CA3AF;">
                                Produk tidak ditemukan.
                            </div>`);
                return;
            }

            res.products.forEach(function (p) {
                $grid.append(buildProductCard(p));
            });

            buildProductPagination(res.current_page, res.last_page);
        });
    }

    /* Category click */
    $(document).on('click', '.cat-pill', function () {
        activeCat = $(this).data('cat') || 'all';
        $('.cat-pill').css({ background:'#fff', color:'#555', borderColor:'var(--color-border)' });
        $(this).css({ background:'var(--color-primary)', color:'#fff', borderColor:'var(--color-primary)' });
        loadProducts(1);
    });

    /* Search input with debounce */
    $('#product-search').on('input', function () {
        clearTimeout(productTimer);
        productTimer = setTimeout(() => loadProducts(1), 300);
    });

    /* Pagination button click (event delegation — buttons are rendered by JS) */
    $(document).on('click', '.prod-page-btn', function () {
        const page = parseInt($(this).data('page'), 10);
        if (!isNaN(page) && page > 0) loadProducts(page);
    });

    /* Initial load */
    loadProducts(1);

    /* ══════════════════════════════════════════════════════════════
       CUSTOMER SEARCH — shared dropdown builder
    ══════════════════════════════════════════════════════════════ */
    function buildCustomerDropdown($dd, users, onSelect) {
        $dd.empty();
        if (!users.length) {
            $dd.append(
                '<div style="padding:10px 14px;font-size:12px;color:#9CA3AF;">' +
                'Tidak ditemukan — ketik untuk walk-in</div>'
            );
        } else {
            users.forEach(function (u) {
                const displayPhone = u.username || '';
                const label = u.name + (displayPhone ? ' — ' + displayPhone : '');
                $dd.append(
                    $('<div>')
                        .css({ padding:'10px 14px', cursor:'pointer', fontSize:'12px',
                               fontWeight:'600', color:'var(--color-black)',
                               borderBottom:'1px solid #F0F0F0', lineHeight:'1.4' })
                        .html('<span>' + escHtml(label) + '</span>')
                        .on('mouseenter', function () { $(this).css('background','#F9FAFB'); })
                        .on('mouseleave', function () { $(this).css('background','#fff'); })
                        .on('click', function () {
                            onSelect(u);
                            $dd.hide();
                        })
                );
            });
        }
        $dd.show();
    }

    /* Phone input search */
    $('#customer-phone-input').on('input', function () {
        clearTimeout(phoneTimer);
        const q = $(this).val().trim();
        if (q.length < 2) { $('#phone-dropdown').hide().empty(); return; }

        phoneTimer = setTimeout(function () {
            $.get('{{ route("cashier.search-customer") }}', { phone: q }, function (users) {
                buildCustomerDropdown($('#phone-dropdown'), users, function (u) {
                    customerId = u.id;
                    $('#customer-name-input').val(u.name);
                    $('#customer-phone-input').val(u.username || '');
                    $('#name-dropdown').hide();
                });
            });
        }, 300);
    });

    /* Name input search */
    $('#customer-name-input').on('input', function () {
        clearTimeout(nameTimer);
        const q = $(this).val().trim();
        /* reset customerId if cashier is typing a new name */
        customerId = null;
        if (q.length < 2) { $('#name-dropdown').hide().empty(); return; }

        nameTimer = setTimeout(function () {
            $.get('{{ route("cashier.search-customer") }}', { q: q }, function (users) {
                buildCustomerDropdown($('#name-dropdown'), users, function (u) {
                    customerId = u.id;
                    $('#customer-name-input').val(u.name);
                    $('#customer-phone-input').val(u.username || '');
                    $('#phone-dropdown').hide();
                });
            });
        }, 300);
    });

    /* Close all dropdowns on outside click */
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#phone-wrap,#name-wrap').length) {
            $('#phone-dropdown,#name-dropdown').hide();
        }
    });

    /* ══════════════════════════════════════════════════════════════
       ORDER TYPE RADIO
    ══════════════════════════════════════════════════════════════ */
    function syncOrderTypeStyles() {
        $('.order-type-radio').each(function () {
            const $lbl = $(this).siblings('.order-type-label');
            if ($(this).is(':checked')) {
                $lbl.css({ borderColor:'var(--color-primary)', color:'var(--color-primary)', background:'#FFF5F5' });
            } else {
                $lbl.css({ borderColor:'#E5E7EB', color:'#9CA3AF', background:'#fff' });
            }
        });
    }

    $(document).on('change', '.order-type-radio', function () {
        orderType = $(this).val();
        syncOrderTypeStyles();
    });

    syncOrderTypeStyles();

    /* ══════════════════════════════════════════════════════════════
       SAVE CUSTOMER
    ══════════════════════════════════════════════════════════════ */
    $('#btn-save-customer').on('click', function () {
        customerName  = $('#customer-name-input').val().trim();
        customerPhone = $('#customer-phone-input').val().trim();

        if (!customerName) {
            $('#customer-name-input').css('border-color', 'var(--color-primary)').focus();
            return;
        }

        orderType     = $('input[name="order_type"]:checked').val() || 'takeaway';
        customerSaved = true;

        $('#customer-form-state').hide();
        $('#customer-saved-state').show();
        $('#saved-customer-name').text(customerName);
        $('#saved-customer-phone').text(customerPhone || '');
        $('#saved-order-type').text(orderType === 'dine-in' ? 'Dine-in' : 'Takeaway');

        updateOrderButton();
    });

    $('#btn-edit-customer').on('click', function () {
        customerSaved = false;
        $('#customer-saved-state').hide();
        $('#customer-form-state').show();
        updateOrderButton();
    });

    /* ══════════════════════════════════════════════════════════════
       ADD TO CART
    ══════════════════════════════════════════════════════════════ */
    $(document).on('click', '.product-card', function () {
        if (!customerSaved) {
            $('#customer-box').css('border-color','var(--color-primary)');
            setTimeout(() => $('#customer-box').css('border-color','#FFBE54'), 800);
            return;
        }

        const id           = $(this).data('id');
        const name         = String($(this).data('name') || '');
        const price        = parseInt($(this).data('price'), 10) || 0;
        const stock        = parseInt($(this).data('stock'), 10) || 0;
        const isAvailable  = $(this).data('available') !== '0';
        const img          = $(this).data('img') || '';

        if (!isAvailable || stock <= 0) return;

        if (cart[id]) {
            if (cart[id].qty >= stock) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stok Tidak Mencukupi!',
                    text: 'Maaf, Anda tidak bisa memesan lebih dari sisa stok yang tersedia (' + stock + ' pcs).',
                    confirmButtonColor: '#a81d1d',
                });
                return;
            }
            cart[id].qty++;
        } else {
            cart[id] = { id, name, price, qty: 1, stock, img };
        }

        renderCart();
    });

    /* ══════════════════════════════════════════════════════════════
       CART RENDER
    ══════════════════════════════════════════════════════════════ */
    function renderCart() {
        const $wrapper = $('#cart-wrapper');
        const keys     = Object.keys(cart);

        /* Remove all item rows (keep #cart-empty) */
        $wrapper.find('.cart-row').remove();

        if (!keys.length) {
            $('#cart-empty').show();
            $('#cart-summary').hide();
            updateTotals(0, 0, 0);
            updateOrderButton();
            return;
        }

        $('#cart-empty').hide();

        let subtotal = 0;
        keys.forEach(function (id) {
            const item    = cart[id];
            const lineAmt = item.price * item.qty;
            subtotal     += lineAmt;

            const $row = $('<div>').addClass('cart-row').css({
                display:'flex', alignItems:'center', gap:'10px',
                padding:'9px 0', borderBottom:'1px solid #F5F5F5'
            });

            /* Thumb */
            const $thumb = $('<div>').css({
                width:'38px', height:'38px', borderRadius:'50%',
                background:'#FDECD8', flexShrink:'0',
                display:'flex', alignItems:'center', justifyContent:'center'
            });
            if (item.img) {
                $thumb.append(
                    $('<img>').attr('src', item.img).css({ width:'30px', height:'30px', objectFit:'contain' })
                              .on('error', function () { $(this).parent().css('background','#F3F4F6'); })
                );
            }

            /* Info */
            const $info = $('<div>').css({ flex:'1', minWidth:'0' });
            $info.append(
                $('<p>').css({ fontSize:'12px', fontWeight:'700', color:'var(--color-black)',
                               margin:'0 0 1px', whiteSpace:'nowrap', overflow:'hidden', textOverflow:'ellipsis' })
                        .text(item.name)
            );
            $info.append(
                $('<p>').css({ fontSize:'11px', color:'var(--color-primary)', fontWeight:'700', margin:'0' })
                        .text(fmtRp(item.price))
            );

            /* Qty controls */
            const $qty = $('<div>').css({ display:'flex', alignItems:'center', gap:'5px', flexShrink:'0' });

            const $dec = $('<button>').attr('type','button')
                .css({ width:'22px', height:'22px', borderRadius:'50%', border:'1.5px solid #E5E7EB',
                       background:'#fff', cursor:'pointer', display:'flex', alignItems:'center',
                       justifyContent:'center', color:'#555', fontSize:'14px', lineHeight:'1' })
                .text('−')
                .on('click', function () {
                    if (cart[id].qty > 1) { cart[id].qty--; } else { delete cart[id]; }
                    renderCart();
                });

            const $num = $('<span>').css({ fontSize:'12px', fontWeight:'800',
                                           color:'var(--color-black)', minWidth:'16px', textAlign:'center' })
                                    .text(item.qty);

            const $inc = $('<button>').attr('type','button')
                .css({ width:'22px', height:'22px', borderRadius:'50%', border:'none',
                       background:'var(--color-primary)', cursor:'pointer', display:'flex',
                       alignItems:'center', justifyContent:'center', color:'#fff',
                       fontSize:'14px', lineHeight:'1' })
                .text('+')
                .on('click', function () {
                    const maxStock = cart[id].stock || 0;
                    if (maxStock > 0 && cart[id].qty >= maxStock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Tidak Mencukupi!',
                            text: 'Maaf, Anda tidak bisa memesan lebih dari sisa stok yang tersedia (' + maxStock + ' pcs).',
                            confirmButtonColor: '#a81d1d',
                        });
                        return;
                    }
                    cart[id].qty++;
                    renderCart();
                });

            $qty.append($dec, $num, $inc);
            $row.append($thumb, $info, $qty);
            $wrapper.append($row);
        });

        const tax   = Math.round(subtotal * 0.11);
        const total = subtotal + tax;
        updateTotals(subtotal, tax, total);
        $('#cart-summary').show();
        updateOrderButton();
    }

    function updateTotals(subtotal, tax, total) {
        $('#pos-subtotal').text(fmtRp(subtotal));
        $('#pos-tax').text(fmtRp(tax));
        $('#pos-total').text(fmtRp(total));
    }

    function updateStoreBadge(data) {
        const isOpen = data.is_open;
        storeIsOpen  = isOpen;
        $('#store-status-badge').css(isOpen
            ? { background:'#dcfce7', color:'#166534' }
            : { background:'#fee2e2', color:'#991b1b' });
        $('#store-status-dot').css('background', isOpen ? '#22c55e' : '#ef4444');
        $('#store-status-text').text(isOpen ? 'Toko Buka' : 'Toko Tutup');
    }

    function updateOrderButton() {
        const $btn  = $('#btn-order');
        const ready = customerSaved && Object.keys(cart).length > 0 && storeIsOpen;

        if (!storeIsOpen) {
            $btn.text('Toko Sedang Tutup')
                .css({ opacity:'1', cursor:'not-allowed', background:'#6B7280' });
        } else {
            $btn.text('Order')
                .css({ background:'var(--color-primary)',
                       opacity: ready ? '1' : '0.4',
                       cursor:  ready ? 'pointer' : 'not-allowed' });
        }
    }

    /* ══════════════════════════════════════════════════════════════
       PAYMENT MODAL
    ══════════════════════════════════════════════════════════════ */
    $('#btn-order').on('click', function () {
        if (!storeIsOpen) {
            Swal.fire({
                icon: 'error',
                title: 'Toko Tutup',
                text: 'Toko sedang tutup. Tidak dapat memproses pesanan saat ini.',
                confirmButtonColor: '#a81d1d',
            });
            return;
        }
        if (!customerSaved || !Object.keys(cart).length) return;

        const subtotal = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
        const tax      = Math.round(subtotal * 0.11);
        const total    = subtotal + tax;

        $('#modal-customer').text(customerName);
        $('#modal-order-type').text(orderType === 'dine-in' ? 'Dine-in' : 'Takeaway');
        $('#modal-subtotal').text(fmtRp(subtotal));
        $('#modal-tax').text(fmtRp(tax));
        $('#modal-total').text(fmtRp(total));

        $('input[name="payment_method"][value="Cash"]').prop('checked', true).trigger('change');
        $('#payment-modal').css('display', 'flex');
    });

    $('#btn-close-payment').on('click', () => $('#payment-modal').css('display', 'none'));

    $('#payment-modal').on('click', function (e) {
        if ($(e.target).is('#payment-modal')) $(this).css('display', 'none');
    });

    $(document).on('change', '.payment-radio', function () {
        $('.payment-method-label').css({ borderColor:'#E5E7EB', color:'#9CA3AF', background:'#fff' });
        $(this).siblings('.payment-method-label')
               .css({ borderColor:'var(--color-primary)', color:'var(--color-primary)', background:'#FFF5F5' });
    });

    /* ══════════════════════════════════════════════════════════════
       CONFIRM & SUBMIT
    ══════════════════════════════════════════════════════════════ */
    $('#btn-confirm-order').on('click', function () {
        const paymentMethod = $('input[name="payment_method"]:checked').val() || 'Cash';

        const items = Object.values(cart).map(i => ({
            product_id: i.id, qty: i.qty, price: i.price
        }));

        const payload = {
            customer_name  : customerName,
            customer_phone : customerPhone,
            customer_id    : customerId,
            order_type     : orderType,
            payment_method : paymentMethod,
            items          : items,
        };

        $('#payment-modal').css('display', 'none');
        $('#processing-modal').css('display', 'flex');

        $.ajax({
            type        : 'POST',
            url         : '{{ route("cashier.orders.store") }}',
            contentType : 'application/json',
            data        : JSON.stringify(payload),
            success     : function (res) {
                $('#processing-modal').css('display', 'none');
                if (!res.success) {
                    Swal.fire({
                        icon: 'error', title: 'Gagal',
                        text: res.message || 'Gagal menyimpan order. Silakan coba lagi.',
                        confirmButtonColor: '#a81d1d',
                    });
                    return;
                }

                if (res.snap_token) {
                    window.snap.pay(res.snap_token, {
                        onSuccess: function () {
                            $.ajax({
                                type    : 'POST',
                                url     : '{{ route("cashier.orders.mark-qris-paid", ":id") }}'.replace(':id', res.order_id),
                                data    : { _token: '{{ csrf_token() }}' },
                                success : function (paidRes) {
                                    if (paidRes.success) { showReceipt(paidRes); }
                                },
                                error   : function () {
                                    Swal.fire('Pembayaran Diterima',
                                        'Pembayaran QRIS berhasil. Order #' + res.order_number + ' sedang diproses.',
                                        'success');
                                },
                            });
                        },
                        onPending: function () {
                            Swal.fire({
                                icon: 'info', title: 'Menunggu Pembayaran',
                                text: 'Pembayaran QRIS untuk order ' + res.order_number + ' sedang menunggu konfirmasi.',
                                confirmButtonColor: '#a81d1d',
                            });
                        },
                        onError: function () {
                            Swal.fire({
                                icon: 'error', title: 'Pembayaran Gagal',
                                text: 'Pembayaran QRIS tidak berhasil. Silakan coba metode lain.',
                                confirmButtonColor: '#a81d1d',
                            });
                        },
                        onClose: function () {
                            Swal.fire({
                                icon: 'warning', title: 'Pembayaran Dibatalkan',
                                text: 'Popup QRIS ditutup. Konfirmasi pembayaran secara manual atau pilih metode lain.',
                                confirmButtonColor: '#a81d1d',
                            });
                        },
                    });
                } else {
                    showReceipt(res);
                }
            },
            error       : function (xhr) {
                $('#processing-modal').css('display', 'none');
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg, confirmButtonColor: '#a81d1d' });
            },
        });
    });

    /* ══════════════════════════════════════════════════════════════
       RECEIPT MODAL
    ══════════════════════════════════════════════════════════════ */
    function showReceipt(res) {
        lastOrderId = res.order_id || null;

        $('#receipt-order-no').text(res.order_number || '—');
        $('#receipt-customer').text(res.customer || '—');
        $('#receipt-order-type').text(res.order_type === 'dine-in' ? 'Dine-in' : 'Takeaway');
        $('#receipt-payment').text(res.payment || '—');
        $('#receipt-subtotal').text(fmtRp(res.subtotal));
        $('#receipt-tax').text(fmtRp(res.tax));
        $('#receipt-total').text(fmtRp(res.total));

        if (res.customer_phone) {
            $('#receipt-phone').text(res.customer_phone);
            $('#receipt-phone-row').css('display','flex');
            // Show WA button only when a phone number is present
            $('#btn-send-wa').show();
        } else {
            $('#receipt-phone-row').hide();
            $('#btn-send-wa').hide();
        }

        // Reset WA button to idle state
        $('#btn-send-wa-label').show();
        $('#btn-send-wa-loading').hide();
        $('#btn-send-wa').prop('disabled', false).css('opacity','1');

        const $items = $('#receipt-items').empty();
        (res.items || []).forEach(function (item) {
            $items.append(
                $('<div>').css({ display:'flex', justifyContent:'space-between',
                                 fontSize:'12px', marginBottom:'6px' })
                          .append(
                              $('<span>').css({ color:'var(--color-black)', fontWeight:'600' })
                                         .text(item.qty + '× ' + item.name)
                          )
                          .append(
                              $('<span>').css({ color:'#555', fontWeight:'600' })
                                         .text(fmtRp(item.subtotal))
                          )
            );
        });

        $('#success-modal').css('display', 'flex');
    }

    /* ── Send WhatsApp receipt ─────────────────────────────────── */
    $('#btn-send-wa').on('click', function () {
        if (!lastOrderId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).css('opacity', '0.7');
        $('#btn-send-wa-label').hide();
        $('#btn-send-wa-loading').show();

        $.ajax({
            type : 'POST',
            url  : '/cashier/orders/' + lastOrderId + '/send-whatsapp',
            success: function (res) {
                $('#btn-send-wa-label').show();
                $('#btn-send-wa-loading').hide();
                $btn.prop('disabled', false).css('opacity', '1');

                if (res.success) {
                    $btn.text('✓ Struk Terkirim!').prop('disabled', true)
                        .css({ background:'#16A34A', opacity:'1' });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Kirim WA',
                        text: res.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#a81d1d',
                    });
                }
            },
            error: function (xhr) {
                $('#btn-send-wa-label').show();
                $('#btn-send-wa-loading').hide();
                $btn.prop('disabled', false).css('opacity', '1');

                const msg = xhr.responseJSON?.message || 'Koneksi ke server gagal.';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Kirim WA',
                    text: msg,
                    confirmButtonColor: '#a81d1d',
                });
            },
        });
    });

    function resetPos() {
        cart          = {};
        customerId    = null;
        customerName  = '';
        customerPhone = '';
        customerSaved = false;
        orderType     = 'takeaway';
        lastOrderId   = null;

        $('#customer-name-input').val('').css('border-color','#FFD080');
        $('#customer-phone-input').val('').css('border-color','#FFD080');
        $('#customer-saved-state').hide();
        $('#customer-form-state').show();
        $('input[name="order_type"][value="takeaway"]').prop('checked', true);
        syncOrderTypeStyles();

        renderCart();
        $('#success-modal').css('display', 'none');
    }

    $('#btn-new-order').on('click', resetPos);
    $('#btn-close-success').on('click', resetPos);

    /* Initial render */
    renderCart();
    updateOrderButton();

    /* Poll store status every 30 s so the badge and Order button stay in sync */
    setInterval(function () {
        $.get('{{ route("store.status") }}', function (data) {
            updateStoreBadge(data);
            updateOrderButton();
        });
    }, 30000);

});
</script>
@endpush
