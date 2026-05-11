<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Corndogku</title>
    
    {{-- Memanggil Tailwind CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
@php
    // ── Demo data — replace with controller-injected variables ────────────
    $menuItems = [
        ['name' => 'Double Cheese',  'description' => 'Corndog with melted mozzarella and rich cheddar filling.',        'price' => 'Rp 18.000', 'image' => null],
        ['name' => 'Original',       'description' => 'Classic crispy corndog with our signature golden batter.',         'price' => 'Rp 15.000', 'image' => null],
        ['name' => 'Squid Nori',     'description' => 'Corndog with squid ink batter and crispy nori seaweed coating.',   'price' => 'Rp 20.000', 'image' => null],
        ['name' => 'Mozza Cheese',   'description' => 'Loaded with stretchy mozzarella cheese in every bite.',            'price' => 'Rp 18.000', 'image' => null],
        ['name' => 'Spicy BBQ',      'description' => 'Smoky BBQ sauce with a kick of chili and our crispy batter.',      'price' => 'Rp 17.000', 'image' => null],
        ['name' => 'Honey Butter',   'description' => 'Sweet honey glaze with rich butter inside our golden batter.',     'price' => 'Rp 16.000', 'image' => null],
    ];

    $orders = [
        ['id' => '#12345', 'menu' => 'Original',     'qty' => 1, 'price' => 'Rp 18.000', 'subtotal' => 'Rp 16.000'],
        ['id' => '#12346', 'menu' => 'Squid Nori',   'qty' => 1, 'price' => 'Rp 18.000', 'subtotal' => 'Rp 20.000'],
        ['id' => '#12347', 'menu' => 'Mozza Cheese', 'qty' => 1, 'price' => 'Rp 18.000', 'subtotal' => 'Rp 18.000'],
        ['id' => '#12348', 'menu' => 'Original',     'qty' => 1, 'price' => 'Rp 18.000', 'subtotal' => 'Rp 18.000'],
        ['id' => '#12349', 'menu' => 'Squid Nori',   'qty' => 1, 'price' => 'Rp 18.000', 'subtotal' => 'Rp 18.000'],
    ];

    $calendarDays  = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
    $calendarDates = [12, 13, 14, 15, 16, 17, 18];
    $today         = 18;
@endphp

{{-- ══════════════════════════════════════════════════════════════════════
     Page wrapper — cornsilk bg, sidebar pill floats inside with padding
     ══════════════════════════════════════════════════════════════════════ --}}
<div class="flex gap-6 min-h-screen bg-cornsilk p-6">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <x-layouts.sidebar :active="'dashboard'" />

    {{-- ── Main content ───────────────────────────────────────────────── --}}
    <main class="flex-1 min-w-0 py-3 pr-3 overflow-auto">

        {{-- ┌─────────────────────────────────────────────────────────────┐
             │  HEADER                                                     │
             └─────────────────────────────────────────────────────────────┘ --}}
        <div class="flex items-start gap-4">

            {{-- Mascot logo --}}
            <div class="w-[65px] h-[65px] shrink-0">
                <img
                    src="{{ asset('images/logo-dashboard.png') }}"
                    alt="Corndog-Ku Logo"
                    class="w-full h-full object-contain"
                />
            </div>

            {{-- BAR branch badge + chevron --}}
            <div class="flex items-center gap-2 mt-3">
                <span class="inline-flex items-center bg-red-blood text-white text-sm font-bold px-3 py-1 rounded-full leading-none">
                    BAR
                </span>
                <svg viewBox="0 0 8 14" class="w-2.5 h-3.5 text-red-blood" fill="none">
                    <path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            {{-- Welcome heading + branch address --}}
            <div>
                <h1 class="text-5xl font-bold text-black leading-tight">Welcome to Corndog-Ku!</h1>
                <p class="text-2xl font-normal text-black mt-1">Jl. Rungkut Mejoyo Utara No.61, Blora</p>
            </div>

        </div>

        {{-- ┌─────────────────────────────────────────────────────────────┐
             │  STATS ROW  —  Revenue · Total Orders · Calendar           │
             └─────────────────────────────────────────────────────────────┘ --}}
        <div class="flex gap-7 mt-8">

            {{-- Revenue card --}}
            <div class="flex-1 bg-white rounded-[12px] shadow-[3px_4px_20px_0px_rgba(0,0,0,0.25)] h-[176px] px-8 py-6 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <p class="text-[32px] font-normal text-black leading-normal">Revenue</p>
                    {{-- Dollar icon --}}
                    <div class="w-[43px] h-[43px] rounded-full flex items-center justify-center bg-[rgba(166,23,28,0.1)]">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-red-blood" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M12 6.5v1m0 9v1M9.5 9.5C9.5 8.12 10.62 7 12 7s2.5 1.12 2.5 2.5S13.38 12 12 12s-2.5 1.12-2.5 2.5S10.62 17 12 17s2.5-1.12 2.5-2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[40px] font-bold text-black leading-none">Rp 800.000</p>
            </div>

            {{-- Total Orders card --}}
            <div class="flex-1 bg-white rounded-[12px] shadow-[3px_4px_20px_0px_rgba(0,0,0,0.25)] h-[176px] px-8 py-6 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <p class="text-[32px] font-normal text-black leading-normal">Total Order</p>
                    {{-- Package icon --}}
                    <div class="w-[43px] h-[43px] rounded-full flex items-center justify-center bg-[rgba(166,23,28,0.1)]">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-red-blood" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 8L12 3L3 8M21 8L12 13M21 8V16L12 21M12 13L3 8M12 13V21M3 8V16L12 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[40px] font-bold text-black leading-none">35 orders</p>
            </div>

            {{-- Calendar widget --}}
            <div class="w-[391px] shrink-0 bg-white rounded-[12px] shadow-[3px_4px_20px_0px_rgba(0,0,0,0.25)] h-[176px] px-5 py-4">

                {{-- Month + nav --}}
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xl font-bold text-black">November 2025</p>
                    <div class="flex gap-1">
                        <button class="w-6 h-6 bg-[#eaedf1] rounded-full flex items-center justify-center hover:bg-[#d4d8e0] transition-colors">
                            <svg viewBox="0 0 6 10" class="w-1.5 h-2.5" fill="none">
                                <path d="M5 1L1 5L5 9" stroke="#555" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="w-6 h-6 bg-[#eaedf1] rounded-full flex items-center justify-center hover:bg-[#d4d8e0] transition-colors">
                            <svg viewBox="0 0 6 10" class="w-1.5 h-2.5" fill="none">
                                <path d="M1 1L5 5L1 9" stroke="#555" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Day labels --}}
                <div class="grid grid-cols-7 text-center mb-1">
                    @foreach ($calendarDays as $day)
                        <span class="text-[13px] font-semibold tracking-[-0.078px] text-[rgba(60,60,67,0.3)]">
                            {{ $day }}
                        </span>
                    @endforeach
                </div>

                {{-- Date row --}}
                <div class="grid grid-cols-7 text-center">
                    @foreach ($calendarDates as $date)
                        <div class="flex items-center justify-center">
                            <span @class([
                                'w-[40px] h-[40px] flex items-center justify-center text-xl tracking-[0.38px]',
                                'bg-red-blood text-white rounded-[60px] font-semibold' => $date === $today,
                                'text-black font-normal'                               => $date !== $today,
                            ])>{{ $date }}</span>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>

        {{-- ┌─────────────────────────────────────────────────────────────┐
             │  MENU ITEMS  —  search bar + 3-column card grid            │
             └─────────────────────────────────────────────────────────────┘ --}}
        <div class="mt-10">

            {{-- Section header + search controls --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-black">Menu Items</h2>
                <div class="flex items-center gap-3">
                    <div class="w-[280px]">
                        <x-input placeholder="Search menu..." name="search" />
                    </div>
                    <x-button class="w-[120px]">Search</x-button>
                </div>
            </div>

            {{-- 3-column grid — pt-[29px] reserves space for the cards' overflowing top image circle --}}
            <div class="grid grid-cols-3 gap-x-8 gap-y-14 pt-[29px]">
                @foreach ($menuItems as $index => $item)
                    <div class="flex justify-center">
                        <x-menu-card
                            :name="$item['name']"
                            :description="$item['description']"
                            :price="$item['price']"
                            :image="$item['image']"
                            :selected="$index === 0"
                        />
                    </div>
                @endforeach
            </div>

        </div>

        {{-- ┌─────────────────────────────────────────────────────────────┐
             │  ORDER LIST TABLE                                           │
             └─────────────────────────────────────────────────────────────┘ --}}
        <div class="mt-10 mb-4 bg-white rounded-[12px] shadow-[3px_4px_20px_0px_rgba(0,0,0,0.25)] overflow-hidden">

            {{-- Red header bar — matches Figma: h-[61px], font-semibold, text-[32px] --}}
            <div class="bg-red-blood h-[61px] flex items-center px-8">
                <h2 class="text-[32px] font-semibold text-white leading-[28px]">Order List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e2e8f0]">
                            <th class="py-3 pl-12 pr-3 text-left   text-[24px] font-semibold text-black whitespace-nowrap">Order ID</th>
                            <th class="py-3 px-3          text-center text-[24px] font-semibold text-black whitespace-nowrap">Menu</th>
                            <th class="py-3 px-3          text-center text-[24px] font-semibold text-black whitespace-nowrap">Quantity</th>
                            <th class="py-3 px-3          text-center text-[24px] font-semibold text-black whitespace-nowrap">Price</th>
                            <th class="py-3 pl-3 pr-8     text-center text-[24px] font-semibold text-black whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-b border-[#e2e8f0] last:border-b-0">
                                <td class="py-[18.5px] pl-12 pr-3 text-[20px] font-normal text-black text-center">{{ $order['id'] }}</td>
                                <td class="py-[18.5px] px-3       text-[20px] font-normal text-black text-center">{{ $order['menu'] }}</td>
                                <td class="py-[18.5px] px-3       text-[20px] font-normal text-black text-center">{{ $order['qty'] }}</td>
                                <td class="py-[18.5px] px-3       text-[20px] font-medium text-black text-center">{{ $order['price'] }}</td>
                                <td class="py-[18.5px] pl-3 pr-8  text-[20px] font-medium text-black text-center">{{ $order['subtotal'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</div>

</body>
</html>
