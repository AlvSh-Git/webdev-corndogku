{{--
    Order Detail Drawer — slide-in panel kanan
    IDs: #order-detail-drawer, #close-drawer-btn, #drawer-backdrop
--}}

{{-- ── BACKDROP ──────────────────────────────────────────────────────── --}}
<div id="drawer-backdrop"
     style="position:fixed;inset:0;
            z-index:9998;
            background-color:rgba(0,0,0,0.42);
            display:none;
            pointer-events:auto;"></div>

{{-- ── PANEL ─────────────────────────────────────────────────────────── --}}
<div id="order-detail-drawer"
     style="position:fixed;top:0;right:0;bottom:0;
            width:min(460px,96vw);
            z-index:9999;
            background:#fff;
            box-shadow:-6px 0 40px rgba(0,0,0,0.14);
            display:flex;flex-direction:column;
            transform:translateX(100%);
            transition:transform 0.3s ease-in-out;
            pointer-events:auto;">

    {{-- ════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════ --}}
    <div style="flex:none;display:flex;align-items:flex-start;
                justify-content:space-between;
                padding:18px 20px 16px;
                border-bottom:1px solid #F0F0F0;">

        {{-- Left: icon + title + cashier name + cancellation reason --}}
        <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0;">
            <div style="width:34px;height:34px;
                        background:var(--color-primary-surface);
                        border-radius:8px;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0;margin-top:1px;">
                <svg xmlns="http://www.w3.org/2000/svg"
                     style="width:16px;height:16px;pointer-events:none;"
                     fill="none" viewBox="0 0 24 24"
                     stroke="var(--color-primary)" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10
                             a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2
                             M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <h2 style="font-size:15px;font-weight:700;
                           color:var(--color-black);margin:0 0 2px;">
                    Order Detail
                </h2>
                {{-- Cancellation reason — shown in red when Cancelled --}}
                <p id="drawer-cancel-reason-line"
                   style="display:none;font-size:11px;font-weight:600;
                          color:#DC2626;margin:0;line-height:1.4;">
                    ❌ Alasan Batal:
                    <span id="drawer-cancel-reason-text"
                          style="font-weight:400;"></span>
                </p>
            </div>
        </div>

        {{-- Right: X close button --}}
        <button id="close-drawer-btn"
                type="button"
                aria-label="Tutup"
                style="width:40px;height:40px;
                       border-radius:50%;
                       border:none;
                       background:transparent;
                       cursor:pointer;
                       display:flex;align-items:center;justify-content:center;
                       color:#9CA3AF;
                       flex-shrink:0;
                       transition:background .15s;">
            <svg xmlns="http://www.w3.org/2000/svg"
                 style="width:20px;height:20px;pointer-events:none;"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- SCROLLABLE BODY--}}
    <div style="flex:1;overflow-y:auto;padding:18px 20px 0;">

        {{-- 1. ORDER ID + CUSTOMER — 2-kolom ─────────────────────── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;
                    border:1px solid #EFEFEF;border-radius:12px;
                    overflow:hidden;margin-bottom:12px;">

            <div style="padding:14px 16px;border-right:1px solid #EFEFEF;">
                <p style="font-size:9px;font-weight:600;letter-spacing:.08em;
                           text-transform:uppercase;color:#ABABAB;margin:0 0 6px;">
                    Order ID
                </p>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <span id="drawer-order-id"
                          style="font-family:monospace;font-size:18px;
                                 font-weight:900;color:var(--color-black);">
                        #C3322
                    </span>
                    <span id="drawer-new-badge"
                          style="display:none;font-size:9px;font-weight:900;
                                 padding:2px 6px;border-radius:4px;
                                 background:var(--color-primary);color:#fff;">
                        NEW
                    </span>
                </div>
                {{-- Cashier name — always visible; JS fills the span --}}
                <p style="font-size:10px;color:#6B7280;margin:6px 0 0;line-height:1.4;">
                    <span style="font-weight:600;color:#ABABAB;">Cashier name:</span>
                    <span id="drawer-cashier-name-text"
                          style="font-weight:600;color:var(--color-black);">-</span>
                </p>
            </div>

            <div style="padding:14px 16px;">
                <p style="font-size:9px;font-weight:600;letter-spacing:.08em;
                           text-transform:uppercase;color:#ABABAB;margin:0 0 6px;">
                    Customer
                </p>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;
                                flex-shrink:0;background:#F3F4F6;
                                display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             style="width:14px;height:14px;pointer-events:none;"
                             fill="none" viewBox="0 0 24 24"
                             stroke="#9CA3AF" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0z
                                     M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        {{-- Where the order came from --}}
                        <p id="drawer-order-type"
                           style="font-size:11px;font-weight:600;color:#9CA3AF;margin:0 0 1px;">
                            -
                        </p>
                        {{-- Customer name --}}
                        <p id="drawer-customer-name"
                           style="font-size:13px;font-weight:700;
                                  color:var(--color-black);
                                  line-height:1.3;margin:0;">
                            Customer
                        </p>
                        {{-- Customer phone number --}}
                        <p id="drawer-customer-phone"
                           style="font-size:11px;color:#9CA3AF;margin:1px 0 0;">
                            -
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. PAYMENT / SOURCE / TIME — 3-kolom ─────────────────── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;
                    border:1px solid #EFEFEF;border-radius:12px;
                    overflow:hidden;margin-bottom:18px;">

            <div style="padding:12px 14px;border-right:1px solid #EFEFEF;">
                <p style="font-size:9px;font-weight:600;letter-spacing:.07em;
                           text-transform:uppercase;color:#ABABAB;margin:0 0 4px;">
                    Payment
                </p>
                <p id="drawer-payment"
                   style="font-size:12px;font-weight:700;
                          color:var(--color-black);margin:0;">
                    -
                </p>
            </div>

            <div style="padding:12px 14px;border-right:1px solid #EFEFEF;">
                <p style="font-size:9px;font-weight:600;letter-spacing:.07em;
                           text-transform:uppercase;color:#ABABAB;margin:0 0 4px;">
                    🛵 Source
                </p>
                <p id="drawer-source"
                   style="font-size:12px;font-weight:700;
                          color:var(--color-black);margin:0;">
                    -
                </p>
            </div>

            <div style="padding:12px 14px;">
                <p style="font-size:9px;font-weight:600;letter-spacing:.07em;
                           text-transform:uppercase;color:#ABABAB;margin:0 0 4px;">
                    🕐 Time
                </p>
                <p id="drawer-time"
                   style="font-size:12px;font-weight:700;
                          color:var(--color-black);margin:0 0 1px;">
                    -
                </p>
                <p id="drawer-date"
                   style="font-size:9px;color:#ABABAB;margin:0;">
                    -
                </p>
            </div>
        </div>

        {{-- 3. UPDATE STATUS — Horizontal Stepper ─────────────────── --}}
        <div style="margin-bottom:18px;">

            <div style="display:flex;align-items:center;
                        justify-content:space-between;margin-bottom:14px;">
                <div>
                    <p style="font-size:13px;font-weight:700;
                               color:var(--color-black);margin:0 0 2px;">
                        Update Status
                    </p>
                    <p style="font-size:11px;color:#9CA3AF;margin:0;">
                        Pilih status sesuai progress order
                    </p>
                </div>
                {{-- Batalkan Order button — disabled when Cancelled via JS --}}
                <button id="btn-batalkan-order"
                        type="button"
                        style="display:inline-flex;align-items:center;gap:4px;
                               padding:5px 12px;border-radius:20px;
                               font-size:11px;font-weight:600;cursor:pointer;
                               border:1.5px solid var(--color-primary);
                               color:var(--color-primary);background:transparent;
                               white-space:nowrap;transition:opacity .15s;">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         style="width:10px;height:10px;pointer-events:none;"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batalkan Order
                </button>
            </div>

            @php
                $stepDefs = [
                    ['key'=>'Pending',   'num'=>1, 'label'=>'Pending',
                     'sub'=>'Order diterima',   'c'=>'#F59E0B','bg'=>'#FFFBEB','bc'=>'#FCD34D'],
                    ['key'=>'Preparing', 'num'=>2, 'label'=>'Preparing',
                     'sub'=>'Sedang disiapkan', 'c'=>'#F97316','bg'=>'#FFF7ED','bc'=>'#FDBA74'],
                    ['key'=>'Ready',     'num'=>3, 'label'=>'Ready',
                     'sub'=>'Siap diambil',     'c'=>'#22C55E','bg'=>'#F0FDF4','bc'=>'#86EFAC'],
                    ['key'=>'Completed', 'num'=>4, 'label'=>'Completed',
                     'sub'=>'Selesai',           'c'=>'#6366F1','bg'=>'#EEF2FF','bc'=>'#A5B4FC'],
                ];
            @endphp

            {{-- Stepper wrapper — locked via JS when Cancelled --}}
            <div id="drawer-stepper-wrapper">
                <div id="drawer-stepper"
                     style="position:relative;padding:0 4px;">
                    {{-- track background --}}
                    <div style="position:absolute;top:14px;left:20px;right:20px;
                                 height:2px;background:#E5E7EB;z-index:0;"></div>
                    {{-- track fill --}}
                    <div id="stepper-fill"
                         style="position:absolute;top:14px;left:20px;height:2px;
                                background:#F59E0B;z-index:1;width:0%;
                                transition:width .35s ease;"></div>

                    <div style="position:relative;z-index:2;
                                 display:flex;flex-direction:row;
                                 align-items:flex-start;">
                        @foreach ($stepDefs as $step)
                            <div class="step-item"
                                 data-step="{{ $step['key'] }}"
                                 style="flex:1;display:flex;flex-direction:column;
                                        align-items:center;gap:5px;cursor:pointer;">

                                <div class="step-circle"
                                     data-active-color="{{ $step['c'] }}"
                                     style="width:28px;height:28px;border-radius:50%;
                                            border:2px solid #E5E7EB;background:#fff;
                                            display:flex;align-items:center;
                                            justify-content:center;
                                            font-size:12px;font-weight:800;
                                            color:#D1D5DB;flex-shrink:0;
                                            transition:border-color .2s,color .2s,background .2s;">
                                    {{ $step['num'] }}
                                </div>

                                <div class="step-badge"
                                     data-active-color="{{ $step['c'] }}"
                                     data-active-bg="{{ $step['bg'] }}"
                                     data-active-border="{{ $step['bc'] }}"
                                     style="width:88%;padding:2px 4px;border-radius:6px;
                                            border:1.5px solid #E5E7EB;background:#F9FAFB;
                                            text-align:center;font-size:10px;font-weight:700;
                                            color:#D1D5DB;line-height:1.4;
                                            transition:border-color .2s,color .2s,background .2s;">
                                    {{ $step['label'] }}
                                </div>

                                <p class="step-sub"
                                   style="font-size:9px;font-weight:500;color:#9CA3AF;
                                          text-align:center;line-height:1.3;margin:0;">
                                    {{ $step['sub'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DIVIDER ──────────────────────────────────────────────── --}}
        <div style="border-top:1px solid #F0F0F0;margin-bottom:16px;"></div>

        {{-- 5. ITEMS ────────────────────────────────────────────────── --}}
        <div style="margin-bottom:20px;">
            <p style="font-size:13px;font-weight:700;
                       color:var(--color-black);margin:0 0 12px;">
                Items
            </p>

            <div id="drawer-items"
                 style="max-height:240px;overflow-y:auto;
                        display:flex;flex-direction:column;gap:0;
                        padding-right:2px;">
            </div>
        </div>

    </div>{{-- /body --}}

    {{-- ════════════════════════════════════════════════
         FOOTER — total
    ════════════════════════════════════════════════ --}}
    <div style="flex:none;padding:14px 20px 16px;
                border-top:1px solid #F0F0F0;">

        <div style="display:flex;align-items:center;
                    justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:12px;color:#9CA3AF;">Items</span>
            <span id="drawer-item-count"
                  style="font-size:12px;font-weight:600;color:#9CA3AF;">×0</span>
        </div>

        <div style="display:flex;align-items:center;
                    justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:12px;color:#9CA3AF;">Subtotal</span>
            <span id="drawer-subtotal"
                  style="font-size:12px;font-weight:600;color:#555;">
                Rp 0
            </span>
        </div>

        <div style="display:flex;align-items:center;
                    justify-content:space-between;
                    padding-bottom:10px;
                    border-bottom:1px dashed #EFEFEF;margin-bottom:8px;">
            <span style="font-size:12px;color:#9CA3AF;">Pajak (11%)</span>
            <span id="drawer-tax"
                  style="font-size:12px;font-weight:600;color:#555;">
                Rp 0
            </span>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:700;color:var(--color-black);">
                Total
            </span>
            <span id="drawer-total"
                  style="font-size:20px;font-weight:900;color:var(--color-primary);">
                Rp 0
            </span>
        </div>
    </div>

</div>{{-- /panel --}}
