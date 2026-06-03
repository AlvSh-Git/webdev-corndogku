<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corndog-Ku')</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>.swal2-container { z-index: 999999 !important; }</style>
    @stack('styles')
</head>
<body class="font-sans antialiased" style="background-color: var(--color-light); color: var(--color-black);">

{{-- ══════════════════════════════════════════════════════════════
     CUSTOMER NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">

    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-16 h-16
                flex items-center justify-between gap-4">

        {{-- Brand --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}"
                alt="Corndog-Ku"
                class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-base tracking-tight hidden sm:inline"
          style="color: var(--color-black);">Corndog-Ku</span>
        </a>

        {{-- Search bar (center) — submits to menu; menu page prevents submit for live filtering --}}
        <form action="{{ route('menu') }}" method="GET" id="navbar-search-form"
              class="flex-1 max-w-md mx-auto">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                </svg>
                <input id="navbar-search"
                       type="search"
                       name="q"
                       placeholder="Cari produk…"
                       value="{{ request('q') }}"
                       autocomplete="off"
                       class="w-full pl-9 pr-4 py-2 rounded-full text-sm border
                              focus:outline-none focus:ring-2 focus:ring-red-200"
                       style="border-color: var(--color-border); background-color: #f9f9f9;">
            </div>
        </form>

        {{-- Right: Cart → Avatar → Greeting → Logout --}}
        <div class="flex items-center gap-4 flex-none">

            {{-- Cart button --}}
            <a href="{{ route('cart') }}"
               class="relative w-9 h-9 flex items-center justify-center
                      rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                             2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100
                             4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span id="cart-badge"
                      class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px]
                             font-bold flex items-center justify-center"
                      style="background-color: var(--color-accent);
                             color: var(--color-black);">{{ count(session()->get('cart', [])) }}</span>
            </a>

            @auth
                <a href="{{ route('profile') }}"
                   id="navbar-user-avatar"
                   class="w-9 h-9 rounded-full flex items-center justify-center
                          text-white text-sm font-extrabold transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary);"
                   title="{{ auth()->user()->name }}">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </a>
                <span id="navbar-user-name"
                      class="hidden sm:block text-sm font-semibold"
                      style="color: var(--color-black);">
                    Halo, {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-semibold
                                   border transition-opacity hover:opacity-70"
                            style="border-color: var(--color-border); color: var(--color-black);">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('register') }}"
                   class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-semibold
                          border transition-colors hover:opacity-80"
                   style="border-color: var(--color-border); color: var(--color-black);">
                    Daftar
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex px-4 py-2 rounded-full text-sm font-semibold
                          transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary); color: var(--color-white);">
                    Log In
                </a>
            @endauth

        </div>{{-- /.right --}}

    </div>{{-- /.navbar-container --}}

</header>{{-- /.navbar --}}

{{-- BANNER TOKO TUTUP (Ditaruh tepat di bawah </header>) --}}
@php $storeInfo = $storeInfo ?? ['is_open' => true, 'reason' => 'schedule', 'reopen_day' => '', 'reopen_time' => '']; @endphp
    @if (!$storeInfo['is_open'])
    <div id="store-closed-banner"
         class="w-full py-3 px-4 flex items-center justify-center gap-2 text-sm font-semibold"
         style="background-color:#FEF3C7; color:#92400E; border-top: 1px solid #FDE68A; border-bottom: 2px solid #FDE68A;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-none" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                     4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>
            Maaf, toko sedang tutup.
            @if ($storeInfo['reopen_day'] && $storeInfo['reopen_time'])
                Toko akan buka kembali pada
                <strong>{{ $storeInfo['reopen_day'] }}</strong> pukul
                <strong>{{ $storeInfo['reopen_time'] }}</strong>.
            @endif
        </span>
    </div>
    @endif


{{-- ══════════════════════════════════════════════════════════════
     PAGE BODY
══════════════════════════════════════════════════════════════ --}}
<main>
    @yield('content')
</main>


{{-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer style="background-color: var(--color-primary);">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            {{-- Brand column --}}
            <div>
                <img src="{{ asset('assets/img/logo.png') }}"
                     alt="Corndog-Ku"
                     class="w-14 h-14 rounded-full object-cover border-2 border-white/30 mb-4">
                
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Contact Us</h4>
                <ul class="space-y-2 text-sm text-white/70">
                    <li>@corndogku_id</li>
                    <li>+62 823-2511-0652</li>
                    <li class="pt-1">{{ config('store.address') }}</li>
                </ul>
            </div>

            {{-- Follow Us --}}
            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Follow Us</h4>
                <div class="flex items-center gap-3">
                    <a href="https://api.whatsapp.com/send/?phone=6282325110652&text&type=phone_number&app_absent=0"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center
                              justify-center hover:border-white transition-colors"
                       aria-label="WhatsApp">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/corndogku_id?igsh=cDN6b2w0dGwydjI3"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center
                              justify-center hover:border-white transition-colors"
                       aria-label="Instagram">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>{{-- /.grid --}}

        <div class="mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3
                    text-xs text-white/40"
             style="border-top: 1px solid rgba(255,255,255,0.15);">
            <span>&copy; {{ date('Y') }} Corndog-Ku. All rights reserved.</span>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                <span class="text-white/20">|</span>
                <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
            </div>
        </div>

    </div>{{-- /.footer-container --}}
</footer>


@stack('scripts')
</body>
</html>
