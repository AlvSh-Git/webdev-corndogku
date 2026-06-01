<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corndog-Ku') — Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Force SweetAlert2 above the order-detail drawer (z-index: 9999) and its backdrop (9998) */
        .swal2-container { z-index: 999999 !important; }
    </style>
</head>
<body class="font-sans antialiased">

@php
    $role     = $role ?? 'owner';
    $ownerNav = [
        ['route' => 'owner.dashboard', 'label' => 'Dashboard',       'icon' => 'icon-dashboard.svg'],
        ['route' => 'owner.products',  'label' => 'Product',          'icon' => 'icon-product.svg'],
        ['route' => 'owner.reports',   'label' => 'Report',           'icon' => 'icon-report.svg'],
        ['route' => 'owner.users',     'label' => 'User Maintenance', 'icon' => 'icon-user.svg'],
        ['route' => 'owner.jadwal',    'label' => 'Jadwal Operasional', 'icon' => 'icon-schedule.svg'],
    ];
    $cashierNav = [
        ['route' => 'cashier.dashboard', 'label' => 'Dashboard', 'icon' => 'icon-dashboard.svg'],
        ['route' => 'cashier.purchase',  'label' => 'Cashier',   'icon' => 'icon-purchase.svg'],
    ];
    $navItems     = $role === 'cashier' ? $cashierNav : $ownerNav;
    $currentRoute = Route::currentRouteName();
    $pageTitle    = 'Dashboard';
    foreach ($navItems as $item) {
        if ($currentRoute === $item['route']) { $pageTitle = $item['label']; break; }
    }
@endphp

<div class="flex h-screen" style="background-color: var(--color-light);">

    {{-- Backdrop overlay --}}
    <div id="sidebar-backdrop"
         class="hidden fixed inset-0 z-40 bg-black/40"
         aria-hidden="true"></div>

    {{-- ════════════════════════════════════════════════════════════
         SIDEBAR — off-canvas
    ════════════════════════════════════════════════════════════ --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-56
                  -translate-x-full transition-transform duration-300 ease-in-out"
           style="background-color: var(--color-accent);
                  border-radius: 0 2rem 2rem 0;
                  box-shadow: 4px 0 24px rgba(0,0,0,0.12);">

        {{-- Red header with logo --}}
        <div class="relative flex flex-col items-center justify-center flex-none pt-5 pb-4"
             style="background-color: var(--color-primary);
                    border-radius: 0 2rem 2rem 0;
                    min-height: 168px;">

            {{-- Close (X) button --}}
            <button id="sidebar-close-btn"
                    type="button"
                    aria-label="Close navigation"
                    class="absolute top-3 right-3 w-8 h-8
                           flex items-center justify-center rounded-lg
                           hover:bg-white/20 transition-colors focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <img src="{{ asset('assets/img/logo.png') }}"
                 alt="Corndog-Ku"
                 class="w-24 h-24 object-contain rounded-full">

            <span class="mt-1.5 text-[10px] font-bold tracking-widest px-3 py-0.5 rounded-full"
                  style="background-color: rgba(0,0,0,0.18); color: var(--color-white);">
                {{ strtoupper($role) }}
            </span>
        </div>

        {{-- Nav items --}}
        <nav class="flex-1 flex flex-col gap-0.5 px-3 pt-3 pb-4 overflow-y-auto">
            @foreach ($navItems as $item)
                @php $isActive = ($currentRoute === $item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="sidebar-nav-link relative flex items-center gap-3 px-3 py-3 rounded-xl
                          transition-colors min-h-[44px] focus:outline-none"
                   style="{{ $isActive ? 'background-color: rgba(0,0,0,0.12);' : '' }}"
                   aria-current="{{ $isActive ? 'page' : 'false' }}">

                    @if ($isActive)
                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full"
                              style="background-color: var(--color-white);"></span>
                    @endif

                    <img src="{{ asset('assets/ui/' . $item['icon']) }}"
                         alt="" class="w-5 h-5 flex-none"
                         style="{{ $isActive ? 'opacity:1;' : 'opacity:0.75;' }}">

                    <span class="text-xs {{ $isActive ? 'font-bold' : 'font-semibold' }} tracking-wide"
                          style="color: var(--color-black);">
                        {{ strtoupper($item['label']) }}
                    </span>
                </a>
            @endforeach
        </nav>

        {{-- Log Out --}}
        <div class="px-4 pb-5 flex-none">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 rounded-full text-sm font-bold tracking-wide
                               transition-opacity hover:opacity-90 min-h-[44px]"
                        style="background-color: var(--color-primary); color: var(--color-white);">
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ════════════════════════════════════════════════════════════
         MAIN CONTENT AREA
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOP NAVBAR --}}
        <header class="flex-none h-16 flex items-center justify-between
                       px-4 sm:px-6 lg:px-8 border-b"
                style="background-color: var(--color-white);
                       border-color: var(--color-border);
                       box-shadow: 0 1px 4px rgba(0,0,0,0.06);">

            {{-- Left: hamburger + brand --}}
            <div class="flex items-center gap-3">
                <button id="toggle-btn"
                        type="button"
                        aria-label="Toggle navigation"
                        class="w-9 h-9 flex flex-col justify-center items-center gap-1.5
                               rounded-lg hover:bg-gray-100 transition-colors focus:outline-none">
                    <span class="block w-5 h-0.5 rounded-full bg-gray-700"></span>
                    <span class="block w-5 h-0.5 rounded-full bg-gray-700"></span>
                    <span class="block w-5 h-0.5 rounded-full bg-gray-700"></span>
                </button>

                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/img/logo.png') }}"
                         alt=""
                         class="w-9 h-9 object-contain rounded-full"
                         style="background-color: var(--color-primary);">
                    <span class="font-bold text-base tracking-widest hidden sm:inline"
                          style="color: var(--color-primary);">CORNDOG-KU</span>
                </div>
            </div>

            {{-- Right: page title + role chip --}}
            
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

    </div>
</div>

<script>
$(function () {

    /* ── Sidebar open/close ─────────────────────────────────── */
    function openSidebar() {
        $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
        $('#sidebar-backdrop').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeSidebar() {
        $('#sidebar').addClass('-translate-x-full').removeClass('translate-x-0');
        $('#sidebar-backdrop').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $('#toggle-btn').on('click', function () {
        if ($('#sidebar').hasClass('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    $('#sidebar-close-btn, #sidebar-backdrop').on('click', closeSidebar);

    /* Close when clicking a nav link (mobile UX) */
    $('.sidebar-nav-link').on('click', closeSidebar);

    /* Esc key */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

});
</script>

@stack('scripts')
</body>
</html>
