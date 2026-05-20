{{--
    Order Detail Drawer — slide-in panel kanan
    Referensi Figma: node 327:14694
    Dikontrol jQuery: #order-detail-drawer, #close-drawer-btn, #drawer-backdrop
--}}

{{-- ── BACKDROP ────────────────────────────────────────────────── --}}
<div id="drawer-backdrop"
     class="fixed inset-0 z-40 hidden"
     style="background-color: rgba(0,0,0,0.4);"></div>

{{-- ── PANEL ───────────────────────────────────────────────────── --}}
<div id="order-detail-drawer"
     class="fixed top-0 right-0 h-full z-50 flex flex-col
            transition-transform duration-300 ease-in-out translate-x-full"
     style="width: min(460px, 96vw);
            background-color: #fff;
            box-shadow: -8px 0 40px rgba(0,0,0,0.15);">

    {{-- ════════════════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between px-6 py-4 flex-none"
         style="border-bottom: 1px solid #F1F1F1;">
        <div class="flex items-center gap-2.5">
            {{-- Clipboard icon merah --}}
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-none"
                 style="background-color: var(--color-primary-surface);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" style="width:18px;height:18px;"
                     fill="none" viewBox="0 0 24 24" stroke="var(--color-primary)" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="font-bold text-base" style="color: var(--color-black);">Order Detail</h2>
        </div>
        {{-- Tombol close X --}}
        <button id="close-drawer-btn" type="button" aria-label="Tutup panel"
                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors hover:bg-gray-100"
                style="color: #9CA3AF;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         SCROLLABLE BODY
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

        {{-- ── Order ID + Customer Info Card ─────────────────── --}}
        <div class="grid grid-cols-2 rounded-xl overflow-hidden"
             style="border: 1px solid #EFEFEF;">

            {{-- Order ID side --}}
            <div class="px-4 py-4" style="border-right: 1px solid #EFEFEF;">
                <p class="text-[10px] font-semibold tracking-widest uppercase mb-1.5"
                   style="color: #B0B0B0;">Order ID</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xl font-black" style="color: var(--color-black);"
                          id="drawer-order-id">#12353</span>
                    <span id="drawer-new-badge"
                          class="text-[10px] font-black px-2 py-0.5 rounded-md"
                          style="background-color: var(--color-primary); color: #fff;">NEW</span>
                </div>
            </div>

            {{-- Customer side --}}
            <div class="px-4 py-4">
                <p class="text-[10px] font-semibold tracking-widest uppercase mb-1.5"
                   style="color: #B0B0B0;">Customer</p>
                <div class="flex items-center gap-2">
                    {{-- Person icon --}}
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-none"
                         style="background-color: #F3F4F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none"
                             viewBox="0 0 24 24" stroke="#9CA3AF" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-black);"
                           id="drawer-customer-name">Gabriella</p>
                        <p class="text-[11px]" style="color: #9CA3AF;"
                           id="drawer-order-type">Take Away</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Payment / Source / Time Row Card ───────────────── --}}
        <div class="grid grid-cols-3 rounded-xl overflow-hidden"
             style="border: 1px solid #EFEFEF;">

            {{-- Payment Method --}}
            <div class="flex flex-col gap-0.5 px-3 py-3" style="border-right: 1px solid #EFEFEF;">
                <p class="text-[9px] font-semibold tracking-wider uppercase" style="color: #B0B0B0;">
                    Payment Method
                </p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--color-black);"
                   id="drawer-payment">QRIS</p>
            </div>

            {{-- Source --}}
            <div class="flex flex-col gap-0.5 px-3 py-3" style="border-right: 1px solid #EFEFEF;">
                <p class="text-[9px] font-semibold tracking-wider uppercase" style="color: #B0B0B0;">
                    Source
                </p>
                <div class="flex items-center gap-1 mt-0.5">
                    {{-- Delivery bag icon merah --}}
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;flex-shrink:0;"
                         fill="none" viewBox="0 0 24 24" stroke="var(--color-primary)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-sm font-bold" style="color: var(--color-black);"
                       id="drawer-source">Online Order</p>
                </div>
            </div>

            {{-- Time --}}
            <div class="flex flex-col gap-0.5 px-3 py-3">
                <p class="text-[9px] font-semibold tracking-wider uppercase" style="color: #B0B0B0;">
                    Time
                </p>
                <div class="flex items-center gap-1 mt-0.5">
                    {{-- Clock icon kuning --}}
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;flex-shrink:0;"
                         fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-black);"
                           id="drawer-time">11:27 AM</p>
                        <p class="text-[9px]" style="color: #B0B0B0;"
                           id="drawer-date">18 Nov 2025</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Update Status — Horizontal Stepper ─────────────── --}}
        <div>
            <div class="flex items-start justify-between mb-1">
                <div>
                    <p class="text-sm font-bold" style="color: var(--color-black);">Update Status</p>
                    <p class="text-[11px]" style="color: #9CA3AF;">Pilih status sesuai progress order</p>
                </div>
                <button type="button"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[11px] font-semibold border transition-opacity hover:opacity-80 whitespace-nowrap"
                        style="border-color: var(--color-primary); color: var(--color-primary); background: transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                    </svg>
                    Batalkan Order
                </button>
            </div>

            @php
                $stepDefs = [
                    ['key' => 'Pending',   'num' => 1, 'label' => 'Pending',   'sub' => 'Order diterima', 'active_color' => '#FF9E00', 'active_bg' => '#FFF7ED', 'active_border' => '#FF9E00'],
                    ['key' => 'Preparing', 'num' => 2, 'label' => 'Preparing', 'sub' => 'Sedang disiapkan', 'active_color' => '#FF9E00', 'active_bg' => '#FFF7ED', 'active_border' => '#FF9E00'],
                    ['key' => 'Ready',     'num' => 3, 'label' => 'Ready',     'sub' => 'Siap diambil', 'active_color' => '#22C55E', 'active_bg' => '#F0FDF4', 'active_border' => '#22C55E'],
                    ['key' => 'Completed', 'num' => 4, 'label' => 'Completed', 'sub' => 'Selesai', 'active_color' => '#6366F1', 'active_bg' => '#EEF2FF', 'active_border' => '#A5B4FC'],
                ];
            @endphp

            {{-- Stepper track --}}
            <div class="relative flex items-start justify-between mt-4 pb-2" id="drawer-stepper">
                {{-- Connecting line background --}}
                <div class="absolute top-[15px] left-4 right-4 h-0.5 z-0"
                     style="background-color: #E5E7EB;"></div>
                {{-- Connecting line filled (orange up to step 2 for mock) --}}
                <div id="stepper-fill" class="absolute top-[15px] left-4 h-0.5 z-0"
                     style="background-color: #FF9E00; width: 33.33%;"></div>

                @foreach ($stepDefs as $step)
                    <div class="relative z-10 flex flex-col items-center gap-2 flex-1 step-item"
                         data-step="{{ $step['key'] }}">
                        {{-- Numbered circle --}}
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    text-sm font-black border-2 step-circle bg-white"
                             data-active-color="{{ $step['active_color'] }}"
                             style="border-color: #E5E7EB; color: #D1D5DB;">
                            {{ $step['num'] }}
                        </div>

                        {{-- Status badge pill --}}
                        <div class="px-2 py-1 rounded-lg text-[11px] font-bold text-center w-full
                                    border step-badge leading-tight"
                             data-active-color="{{ $step['active_color'] }}"
                             data-active-bg="{{ $step['active_bg'] }}"
                             data-active-border="{{ $step['active_border'] }}"
                             style="border-color: #E5E7EB; color: #D1D5DB; background-color: #F9FAFB;">
                            {{ $step['label'] }}
                        </div>

                        {{-- Sublabel + time --}}
                        <div class="text-center">
                            <p class="text-[10px] font-medium step-sub" style="color: #9CA3AF;">
                                {{ $step['sub'] }}
                            </p>
                            <p class="text-[9px] step-time" style="color: #C4C4C4;"></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Divider ─────────────────────────────────────────── --}}
        <div style="border-top: 1px solid #F1F1F1;"></div>

        {{-- ── Items List ──────────────────────────────────────── --}}
        <div>
            <p class="text-sm font-bold mb-4" style="color: var(--color-black);">Items</p>
            <div id="drawer-items" class="space-y-5">
                {{-- ── Item mock 1 (template structure untuk loop database) --}}
                <div class="flex items-start gap-3 drawer-item">
                    {{-- Product thumbnail --}}
                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-none"
                         style="background-color: #F3F4F6; border: 1px solid #EFEFEF;">
                        <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}" alt=""
                             class="w-full h-full object-cover">
                    </div>
                    {{-- Detail --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold leading-snug" style="color: var(--color-black);">
                                Mix Mozzarella
                            </p>
                            <p class="text-sm font-bold whitespace-nowrap flex-none"
                               style="color: var(--color-black);">Rp 18.000</p>
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: #9CA3AF;">Sosis, Mozzarella</p>
                        <ul class="mt-1 space-y-0.5">
                            <li class="text-[11px]" style="color: #6B7280;">• Ketchup</li>
                            <li class="text-[11px]" style="color: #6B7280;">• Crispy Onion</li>
                        </ul>
                        <div class="flex justify-end mt-1.5">
                            <span class="text-sm font-black" style="color: var(--color-primary);">x2</span>
                        </div>
                    </div>
                </div>

                {{-- ── Item mock 2 --}}
                <div class="flex items-start gap-3 drawer-item">
                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-none"
                         style="background-color: #F3F4F6; border: 1px solid #EFEFEF;">
                        <img src="{{ asset('assets/img/CA_SQUID_NORI.png') }}" alt=""
                             class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold leading-snug" style="color: var(--color-black);">
                                Mix Mozzarella
                            </p>
                            <p class="text-sm font-bold whitespace-nowrap flex-none"
                               style="color: var(--color-black);">Rp 18.000</p>
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: #9CA3AF;">Sosis, Mozzarella</p>
                        <ul class="mt-1 space-y-0.5">
                            <li class="text-[11px]" style="color: #6B7280;">• Ketchup</li>
                            <li class="text-[11px]" style="color: #6B7280;">• Crispy Onion</li>
                        </ul>
                        <div class="flex justify-end mt-1.5">
                            <span class="text-sm font-black" style="color: var(--color-primary);">x2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FOOTER — Total
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex-none px-6 py-4" style="border-top: 1px solid #F1F1F1;">
        <div class="flex items-center justify-between mb-0.5">
            <span class="text-xs" style="color: #9CA3AF;">Items</span>
            <span class="text-xs font-semibold" style="color: #9CA3AF;" id="drawer-item-count">x3</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-base font-bold" style="color: var(--color-black);">Total</span>
            <span class="text-xl font-black" style="color: var(--color-primary);"
                  id="drawer-total">Rp 36.000</span>
        </div>
    </div>

</div>
