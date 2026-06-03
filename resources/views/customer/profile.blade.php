@extends('layouts.customer')

@section('title', 'Profil Saya — Corndog-Ku')

@section('content')

<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">

    {{-- ═══════════════════════════════════════════════════════════
         ALERT BANNER
    ═══════════════════════════════════════════════════════════ --}}
    <div id="profile-alert"
         style="display:none;"
         class="mb-6 px-5 py-4 rounded-xl border">
        <div class="flex items-start justify-between gap-4">
            <span id="profile-alert-msg"
                  class="text-sm font-medium leading-relaxed"></span>
            <button type="button"
                    id="profile-alert-close"
                    class="flex-none text-lg font-bold leading-none mt-0.5
                           hover:opacity-60 transition-opacity">
                &times;
            </button>
        </div>
    </div>

    {{-- ── HERO ── --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-6 mb-8">

        {{-- Avatar --}}
        <div class="relative flex-none self-center sm:self-auto">
            <div id="profile-hero-avatar"
                 class="w-36 h-36 rounded-full flex items-center justify-center
                        text-white text-5xl font-bold select-none"
                 style="background-color: var(--color-primary);
                        box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <button type="button"
                    title="Ganti Foto"
                    class="absolute bottom-0 right-0 w-14 h-14 flex items-center justify-center
                           rounded-full hover:opacity-80 transition-opacity"
                    style="background-color: var(--color-accent);
                           border: 3px solid var(--color-white);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0
                             0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0
                             0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        {{-- Greeting + logout --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between flex-1 gap-4">
            <div class="text-center sm:text-left">
                <p class="text-4xl sm:text-5xl font-bold leading-tight"
                   style="color: var(--color-black);">
                    Halo <span data-hero-name style="color: var(--color-primary);">{{ $user->name }}!</span>
                </p>
                <p class="text-sm mt-1" style="color: #9c9c9c;">
                    Kelola informasi profilmu di sini.
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="self-center sm:self-auto">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2
                               h-10 px-6 font-semibold text-sm transition-opacity hover:opacity-70"
                        style="border: 1px solid var(--color-primary);
                               border-radius: 5px;
                               background-color: var(--color-white);
                               color: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-none" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0
                                 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>

    </div>{{-- /.hero --}}


    {{-- ═══════════════════════════════════════════════════════════
         TAB BAR
    ═══════════════════════════════════════════════════════════ --}}
    <div class="flex mb-8 overflow-hidden"
         style="background-color: rgba(255,255,255,0.9);
                box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);
                border-radius: 15px;">

        <a href="{{ route('profile') }}"
           class="relative flex items-center justify-center gap-2
                  px-8 py-4 flex-1 text-sm font-bold transition-colors"
           style="color: var(--color-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profil Saya</span>
            <div class="absolute bottom-0 left-0 right-0 h-[2px]"
                 style="background-color: var(--color-primary);"></div>
        </a>

        <a href="{{ route('history') }}"
           class="relative flex items-center justify-center gap-2
                  px-8 py-4 flex-1 text-sm font-medium transition-colors"
           style="color: #9c9c9c;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Riwayat Pesanan</span>
        </a>

    </div>{{-- /.tab bar --}}


    <div id="panel-profile">


        {{-- ── ACCOUNT INFO CARD ── --}}
        <div class="rounded-[10px] p-6 sm:p-8"
             style="background-color: var(--color-white);
                    box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);">

            {{-- Card header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-none"
                     style="background-color: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-base" style="color: var(--color-black);">
                    Informasi Akun
                </h3>
            </div>

            <form id="account-info-form" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    {{-- Nama --}}
                    <div>
                        <label for="name"
                               class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Nama</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input type="text"
                                   id="name" name="name"
                                   value="{{ $user->name }}"
                                   class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                   style="border: 1px solid #d9d9d9;
                                          border-radius: 5px;
                                          background-color: var(--color-white);
                                          color: var(--color-black);">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-[14px]
                                           hover:opacity-60 transition-opacity"
                                    title="Edit Nama">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0
                                             113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2
                                             2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input type="email"
                                   id="email" name="email"
                                   value="{{ $user->email }}"
                                   class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                   style="border: 1px solid #d9d9d9;
                                          border-radius: 5px;
                                          background-color: var(--color-white);
                                          color: var(--color-black);">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-[14px]
                                           hover:opacity-60 transition-opacity"
                                    title="Edit Email">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0
                                             113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label for="phone"
                               class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Nomor HP</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1
                                             1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516
                                             5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0
                                             01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </span>
                            <input type="tel"
                                   id="phone" name="phone"
                                   value="{{ $user->phone }}"
                                   class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                   style="border: 1px solid #d9d9d9;
                                          border-radius: 5px;
                                          background-color: var(--color-white);
                                          color: var(--color-black);">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-[14px]
                                           hover:opacity-60 transition-opacity"
                                    title="Edit Nomor HP">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0
                                             113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Password row --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Password</label>
                        <div class="flex gap-3 items-stretch">

                            {{-- Password input with eye toggle --}}
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke="#9c9c9c" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                                 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input type="password"
                                       id="current_password" name="current_password"
                                       placeholder="••••••••"
                                       autocomplete="current-password"
                                       class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                       style="border: 1px solid #d9d9d9;
                                              border-radius: 5px;
                                              background-color: var(--color-white);
                                              color: var(--color-black);">
                                <button type="button"
                                        class="toggle-eye absolute inset-y-0 right-0 flex items-center
                                               pr-[14px] hover:opacity-60 transition-opacity"
                                        data-target="current_password"
                                        tabindex="-1">
                                    <svg class="eye-icon w-4 h-4"
                                         xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268
                                                 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                                 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg class="eye-off-icon w-4 h-4 hidden"
                                         xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                                 0-8.268-2.943-9.543-7a9.97 9.97 0
                                                 011.563-3.029m5.858.908a3 3 0 114.243
                                                 4.243M9.878 9.878l4.242 4.242M9.88
                                                 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3
                                                 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                                 0 8.268 2.943 9.543 7a10.025 10.025 0
                                                 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Ubah Password button --}}
                            <button type="button"
                                    id="open-change-pw"
                                    class="flex-none flex items-center justify-center
                                           py-3 px-5 font-semibold text-sm transition-opacity hover:opacity-70"
                                    style="border: 1px solid var(--color-primary);
                                           border-radius: 5px;
                                           background-color: var(--color-white);
                                           color: var(--color-primary);">
                                Ubah Password
                            </button>

                        </div>
                    </div>{{-- /.password row --}}

                </div>{{-- /.space-y-4 --}}


                {{-- ── Change-password inline panel (collapsed by default) ── --}}
                <div id="change-pw-panel" style="display: none;" class="mt-4 space-y-4">

                    <div>
                        <label for="new_password"
                               class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Password Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                             2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input type="password"
                                   id="new_password" name="new_password"
                                   placeholder="Masukkan password baru"
                                   autocomplete="new-password"
                                   class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                   style="border: 1px solid #d9d9d9;
                                          border-radius: 5px;
                                          background-color: var(--color-white);
                                          color: var(--color-black);">
                            <button type="button"
                                    class="toggle-eye absolute inset-y-0 right-0 flex items-center
                                           pr-[14px] hover:opacity-60 transition-opacity"
                                    data-target="new_password"
                                    tabindex="-1">
                                <svg class="eye-icon w-4 h-4"
                                     xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268
                                             2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                             0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="eye-off-icon w-4 h-4 hidden"
                                     xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                             0-8.268-2.943-9.543-7a9.97 9.97 0
                                             011.563-3.029m5.858.908a3 3 0 114.243
                                             4.243M9.878 9.878l4.242 4.242M9.88
                                             9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3
                                             3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                             0 8.268 2.943 9.543 7a10.025 10.025 0
                                             01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password"
                               class="block text-xs font-semibold mb-1.5"
                               style="color: #9c9c9c;">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-[14px] pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                             2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input type="password"
                                   id="confirm_password" name="confirm_password"
                                   placeholder="Ulangi password baru"
                                   autocomplete="new-password"
                                   class="w-full py-3 pl-10 pr-11 text-sm outline-none"
                                   style="border: 1px solid #d9d9d9;
                                          border-radius: 5px;
                                          background-color: var(--color-white);
                                          color: var(--color-black);">
                            <button type="button"
                                    class="toggle-eye absolute inset-y-0 right-0 flex items-center
                                           pr-[14px] hover:opacity-60 transition-opacity"
                                    data-target="confirm_password"
                                    tabindex="-1">
                                <svg class="eye-icon w-4 h-4"
                                     xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268
                                             2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                             0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="eye-off-icon w-4 h-4 hidden"
                                     xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="#9c9c9c" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                             0-8.268-2.943-9.543-7a9.97 9.97 0
                                             011.563-3.029m5.858.908a3 3 0 114.243
                                             4.243M9.878 9.878l4.242 4.242M9.88
                                             9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3
                                             3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                             0 8.268 2.943 9.543 7a10.025 10.025 0
                                             01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>{{-- /#change-pw-panel --}}


                {{-- Simpan Perubahan --}}
                <div class="mt-6">
                    <button type="button"
                            id="save-info-btn"
                            class="w-full py-3.5 flex items-center justify-center gap-2
                                   font-bold text-sm tracking-wider transition-opacity
                                   hover:opacity-90"
                            style="border-radius: 10px;
                                   background-color: var(--color-primary);
                                   color: var(--color-white);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2
                                     2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>{{-- /.account info card --}}

    </div>{{-- /#panel-profile --}}


</div>{{-- /.profile-wrapper --}}

@endsection


@push('scripts')
<script>
$(function () {

    /* ═══════════════════════════════════════════════════════════
       UBAH PASSWORD TOGGLE
    ═══════════════════════════════════════════════════════════ */
    $('#open-change-pw').on('click', function () {
        var $panel = $('#change-pw-panel');
        $panel.is(':hidden') ? $panel.slideDown(200) : $panel.slideUp(200);
    });

    /* ═══════════════════════════════════════════════════════════
       PASSWORD SHOW / HIDE
    ═══════════════════════════════════════════════════════════ */
    $(document).on('click', '.toggle-eye', function () {
        var $input = $('#' + $(this).data('target'));
        var isPass = $input.attr('type') === 'password';
        $input.attr('type', isPass ? 'text' : 'password');
        $(this).find('.eye-icon').toggleClass('hidden', isPass);
        $(this).find('.eye-off-icon').toggleClass('hidden', !isPass);
    });

    /* ═══════════════════════════════════════════════════════════
       SAVE INFO — AJAX PUT
    ═══════════════════════════════════════════════════════════ */
    $('#save-info-btn').on('click', function () {
        var $btn  = $(this);
        var name  = $.trim($('#name').val());
        var email = $.trim($('#email').val());
        var phone = $.trim($('#phone').val());

        // Client-side guard
        if (!name) {
            showAlert('Nama tidak boleh kosong.', 'error');
            return;
        }

        var pwOpen = $('#change-pw-panel').is(':visible');
        if (pwOpen) {
            var curr = $.trim($('#current_password').val());
            var newP = $.trim($('#new_password').val());
            var conf = $.trim($('#confirm_password').val());
            if (!curr || !newP || !conf) {
                showAlert('Harap isi semua kolom password.', 'error');
                return;
            }
            if (newP.length < 8) {
                showAlert('Password baru minimal 8 karakter.', 'error');
                return;
            }
            if (newP !== conf) {
                showAlert('Password baru dan konfirmasi tidak cocok.', 'error');
                return;
            }
        }

        // Disable button while saving
        $btn.prop('disabled', true).css('opacity', '0.6').find('span, svg').first();
        var origText = $btn.html();
        $btn.html('<span class="inline-flex items-center gap-2">' +
                  '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                  '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                  '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>' +
                  'Menyimpan…</span>');

        var payload = {
            _method          : 'PUT',
            _token           : $('meta[name="csrf-token"]').attr('content'),
            name             : name,
            email            : email,
            phone            : phone,
        };

        if (pwOpen) {
            payload.current_password = $.trim($('#current_password').val());
            payload.new_password     = $.trim($('#new_password').val());
            payload.confirm_password = $.trim($('#confirm_password').val());
        }

        $.ajax({
            type    : 'POST',   // Laravel reads _method=PUT from payload
            url     : '{{ route("profile.update") }}',
            data    : payload,
            success : function (res) {
                $btn.prop('disabled', false).css('opacity', '1').html(origText);

                if (res.success) {
                    showAlert(res.message, 'success');

                    // Reflect updated name instantly across all display points
                    if (res.name) {
                        var initial = res.name.charAt(0).toUpperCase();
                        $('[data-hero-name]').text(res.name + '!');
                        $('#profile-hero-avatar').text(initial);
                        $('#navbar-user-avatar').text(initial).attr('title', res.name);
                        $('#navbar-user-name').text('Halo, ' + res.name);
                    }

                    // Collapse password panel and clear its fields
                    if (pwOpen) {
                        $('#change-pw-panel').slideUp(200);
                        $('#current_password, #new_password, #confirm_password').val('');
                    }
                } else {
                    showAlert(res.message || 'Gagal menyimpan profil.', 'error');
                }
            },
            error : function (xhr) {
                $btn.prop('disabled', false).css('opacity', '1').html(origText);

                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    var first = Object.values(errors)[0];
                    showAlert(Array.isArray(first) ? first[0] : first, 'error');
                } else {
                    var msg = xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    showAlert(msg, 'error');
                }
            },
        });
    });

    /* ═══════════════════════════════════════════════════════════
       ALERT CLOSE
    ═══════════════════════════════════════════════════════════ */
    $('#profile-alert-close').on('click', function () {
        $('#profile-alert').stop(true).fadeOut(300);
    });

    /* ═══════════════════════════════════════════════════════════
       ALERT HELPER
    ═══════════════════════════════════════════════════════════ */
    function showAlert(msg, type) {
        var ok     = (type === 'success');
        var bg     = ok ? '#DCFCE7' : '#FCE8E6';
        var border = ok ? '#34A853' : '#EA4335';
        var color  = ok ? '#34A853' : '#EA4335';

        $('#profile-alert').css({ 'background-color': bg, 'border-color': border });
        $('#profile-alert-msg').css('color', color).text(msg);
        $('#profile-alert-close').css('color', color);

        $('html, body').animate({ scrollTop: $('#profile-alert').offset().top - 80 }, 200);
        $('#profile-alert').stop(true, true).fadeIn(200);

        clearTimeout(window._profileAlertTimer);
        window._profileAlertTimer = setTimeout(function () {
            $('#profile-alert').fadeOut(400);
        }, 4500);
    }

});
</script>
@endpush
