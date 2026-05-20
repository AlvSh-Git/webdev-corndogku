{{--
    Order Detail Drawer — slide-in panel dari kanan
    Dipanggil dari: resources/views/dashboard/employee.blade.php
    Dikontrol oleh jQuery: #order-detail-drawer, #drawer-close-btn
--}}
<div id="order-detail-drawer"
     class="fixed top-0 right-0 h-full z-50 flex flex-col
            transition-transform duration-300 ease-in-out translate-x-full"
     style="width: min(400px, 95vw);
            background-color: var(--color-white);
            box-shadow: -6px 0 32px rgba(0,0,0,0.18);">

    {{-- ── HEADER ────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between px-5 py-4 flex-none"
         style="border-bottom: 1px solid var(--color-border);">
        <div>
            <h2 class="text-base font-bold" style="color: var(--color-black);">Order Detail</h2>
            <p class="text-xs mt-0.5" style="color: #9CA3AF;" id="drawer-subtitle">Informasi pesanan lengkap</p>
        </div>
        <button id="drawer-close-btn" type="button"
                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors hover:bg-gray-100"
                style="color: #6B7280;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── SCROLLABLE BODY ────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

        {{-- Order ID + NEW badge --}}
        <div class="flex items-center gap-2">
            <span class="font-mono font-bold text-lg" style="color: var(--color-black);"
                  id="drawer-order-id">#12353</span>
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                  style="background-color: var(--color-primary); color: #fff;">NEW</span>
        </div>

        {{-- Customer Info --}}
        <div class="flex items-center gap-3 p-3 rounded-xl"
             style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-none"
                 style="background-color: var(--color-primary);" id="drawer-avatar">G</div>
            <div>
                <p class="font-semibold text-sm" style="color: var(--color-black);" id="drawer-customer-name">Gabriella</p>
                <p class="text-xs" style="color: #9CA3AF;" id="drawer-order-type">Take Away</p>
            </div>
        </div>

        {{-- 3-box Info Grid --}}
        <div class="grid grid-cols-3 gap-2">
            {{-- Payment --}}
            <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl text-center"
                 style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background-color: #DCFCE7;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="#15803D" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <p class="text-[10px] font-medium" style="color: #9CA3AF;">Payment</p>
                <p class="text-xs font-bold" style="color: var(--color-black);" id="drawer-payment">QRIS</p>
            </div>
            {{-- Source --}}
            <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl text-center"
                 style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background-color: #EEF2FF;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="#4F46E5" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <p class="text-[10px] font-medium" style="color: #9CA3AF;">Source</p>
                <p class="text-xs font-bold" style="color: var(--color-black);" id="drawer-source">Online</p>
            </div>
            {{-- Time --}}
            <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl text-center"
                 style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background-color: #FEF3C7;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="#D97706" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-[10px] font-medium" style="color: #9CA3AF;">Time</p>
                <p class="text-xs font-bold" style="color: var(--color-black);" id="drawer-time">11:27 AM</p>
            </div>
        </div>

        {{-- Status Stepper --}}
        <div>
            <p class="text-xs font-bold mb-3" style="color: #555;">Update Status</p>
            <div class="relative" id="drawer-stepper">
                {{-- Connector line --}}
                <div class="absolute left-[15px] top-4 bottom-4 w-0.5" style="background-color: #E5E7EB;"></div>

                @php
                    $steps = [
                        ['key' => 'Pending',   'label' => 'Pending',   'sub' => 'Pesanan masuk',       'color' => '#9CA3AF', 'bg' => '#F3F4F6'],
                        ['key' => 'Preparing', 'label' => 'Preparing', 'sub' => 'Sedang diproses',     'color' => '#FF9E00', 'bg' => '#FEF3C7'],
                        ['key' => 'Ready',     'label' => 'Ready',     'sub' => 'Siap diambil',        'color' => '#22C55E', 'bg' => '#DCFCE7'],
                        ['key' => 'Completed', 'label' => 'Completed', 'sub' => 'Pesanan selesai',     'color' => '#4F46E5', 'bg' => '#EEF2FF'],
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach ($steps as $i => $step)
                        <div class="relative flex items-start gap-3 step-item"
                             data-step="{{ $step['key'] }}">
                            {{-- Dot --}}
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-none relative z-10 step-dot"
                                 style="background-color: {{ $step['bg'] }}; border: 2px solid {{ $step['color'] }};">
                                @if ($i === 0)
                                    {{-- Checkmark for first (active by default in mock) --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 step-check hidden" fill="none"
                                         viewBox="0 0 24 24" stroke="{{ $step['color'] }}" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="w-2 h-2 rounded-full step-dot-inner"
                                          style="background-color: {{ $step['color'] }};"></span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 step-check hidden" fill="none"
                                         viewBox="0 0 24 24" stroke="{{ $step['color'] }}" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="w-2 h-2 rounded-full step-dot-inner" style="background-color: #D1D5DB;"></span>
                                @endif
                            </div>
                            {{-- Label --}}
                            <div class="pt-1">
                                <p class="text-xs font-bold step-label" style="color: {{ $step['color'] }};">{{ $step['label'] }}</p>
                                <p class="text-[10px]" style="color: #9CA3AF;">{{ $step['sub'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div style="border-top: 1px dashed var(--color-border);"></div>

        {{-- Items List --}}
        <div>
            <p class="text-xs font-bold mb-3" style="color: #555;">Items Ordered</p>
            <div class="space-y-3" id="drawer-items">
                {{-- Mock items —rendered statically, replaced by JS when real data available --}}
                <div class="flex items-start gap-3 p-3 rounded-xl"
                     style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-none"
                         style="background-color: var(--color-accent);">
                        <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}" alt="Mozza Cheese"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold" style="color: var(--color-black);">Mozza Cheese Corndog</p>
                        <p class="text-[10px] mt-0.5" style="color: #9CA3AF;">Sosis · Full Mozza · Original</p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                  style="background-color: var(--color-primary-surface); color: var(--color-primary);">
                                × 3
                            </span>
                            <span class="text-xs font-bold" style="color: var(--color-black);">Rp 54.000</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl"
                     style="background-color: #F9FAFB; border: 1px solid var(--color-border);">
                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-none"
                         style="background-color: var(--color-accent);">
                        <img src="{{ asset('assets/img/CA_SQUID_NORI.png') }}" alt="Squid Nori"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold" style="color: var(--color-black);">Squid Nori Corndog</p>
                        <p class="text-[10px] mt-0.5" style="color: #9CA3AF;">Cumi · Nori · Ramen Mix</p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                  style="background-color: var(--color-primary-surface); color: var(--color-primary);">
                                × 1
                            </span>
                            <span class="text-xs font-bold" style="color: var(--color-black);">Rp 20.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FOOTER — Grand Total ───────────────────────────────── --}}
    <div class="flex-none px-5 py-4" style="border-top: 1px solid var(--color-border);">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold" style="color: #555;">Grand Total</span>
            <span class="text-lg font-bold" style="color: var(--color-black);" id="drawer-total">Rp 74.000</span>
        </div>
        <button type="button"
                class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition-opacity hover:opacity-90"
                style="background-color: var(--color-primary);">
            Konfirmasi Pembayaran
        </button>
    </div>

</div>

{{-- Backdrop --}}
<div id="drawer-backdrop"
     class="fixed inset-0 z-40 hidden"
     style="background-color: rgba(0,0,0,0.35);">
</div>
