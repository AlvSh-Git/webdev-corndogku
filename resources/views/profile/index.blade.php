@extends('layouts.customer')

@section('title', 'Profil Saya — Corndog-Ku')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">

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

    {{-- ═══════════════════════════════════════════════════════════
         UNDERLINE TAB BAR
    ═══════════════════════════════════════════════════════════ --}}
    <div class="flex mb-8 overflow-hidden"
         style="background-color: rgba(255,255,255,0.9);
                box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);
                border-radius: 15px;">

        <button type="button"
                id="tab-btn-profile"
                class="profile-tab-btn relative flex items-center justify-center gap-2
                       px-8 py-4 flex-1 text-sm font-bold transition-colors"
                data-target="panel-profile"
                style="color: var(--color-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profil Saya</span>
            <div class="tab-underline absolute bottom-0 left-0 right-0 h-[2px]"
                 style="background-color: var(--color-primary);"></div>
        </button>

        <button type="button"
                id="tab-btn-orders"
                class="profile-tab-btn relative flex items-center justify-center gap-2
                       px-8 py-4 flex-1 text-sm font-medium transition-colors"
                data-target="panel-orders"
                style="color: #9c9c9c;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Riwayat Pesanan</span>
            <div class="tab-underline absolute bottom-0 left-0 right-0 h-[2px]"
                 style="background-color: var(--color-primary); display: none;"></div>
        </button>

    </div>{{-- /.tab bar --}}


    {{-- ═══════════════════════════════════════════════════════════
         PANEL 1 — PROFILE
    ═══════════════════════════════════════════════════════════ --}}
    <div id="panel-profile">

        {{-- ── HERO ── --}}
        <div class="flex flex-col sm:flex-row sm:items-end gap-6 mb-10">

            {{-- Avatar --}}
            <div class="relative flex-none self-center sm:self-auto">
                <div class="w-36 h-36 rounded-full flex items-center justify-center
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
                        Halo <span style="color: var(--color-primary);">{{ $user->name }}!</span>
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

            <form id="account-info-form" method="POST" action="#">
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


    {{-- ═══════════════════════════════════════════════════════════
         PANEL 2 — ORDER HISTORY
    ═══════════════════════════════════════════════════════════ --}}
    <div id="panel-orders" style="display:none;">

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
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-base" style="color: var(--color-black);">
                    Riwayat Pesanan
                </h3>
            </div>

            {{-- Status filter chips --}}
            <div class="flex flex-wrap gap-2 mb-6">
                <button type="button"
                        class="order-filter-chip px-4 py-1.5 rounded-full text-xs font-bold
                               transition-all hover:opacity-80"
                        data-filter="semua"
                        style="background-color: var(--color-primary); color: white;">
                    Semua
                </button>
                <button type="button"
                        class="order-filter-chip px-4 py-1.5 rounded-full text-xs font-bold
                               transition-all hover:opacity-80"
                        data-filter="menunggu"
                        style="background-color: #FEF3C7; color: #D97706;">
                    Menunggu
                </button>
                <button type="button"
                        class="order-filter-chip px-4 py-1.5 rounded-full text-xs font-bold
                               transition-all hover:opacity-80"
                        data-filter="diproses"
                        style="background-color: #FFEDD5; color: #EA580C;">
                    Diproses
                </button>
                <button type="button"
                        class="order-filter-chip px-4 py-1.5 rounded-full text-xs font-bold
                               transition-all hover:opacity-80"
                        data-filter="selesai"
                        style="background-color: #DCFCE7; color: #34A853;">
                    Selesai
                </button>
                <button type="button"
                        class="order-filter-chip px-4 py-1.5 rounded-full text-xs font-bold
                               transition-all hover:opacity-80"
                        data-filter="dibatalkan"
                        style="background-color: #FCE8E6; color: #EA4335;">
                    Dibatalkan
                </button>
            </div>

            {{-- Order rows (demo data) --}}
            @php
            $orders = [
                ['id'=>'#CDK-001','date'=>'16 Mei 2026','product'=>'Corndog Original',     'qty'=>2,'total'=>'Rp 36.000','status'=>'selesai',    'img'=>asset('assets/img/CA_ORIGINAL.png')],
                ['id'=>'#CDK-002','date'=>'15 Mei 2026','product'=>'Corndog Mozarella',     'qty'=>1,'total'=>'Rp 23.000','status'=>'diproses',   'img'=>asset('assets/img/CA_FULL_MOZZA.png')],
                ['id'=>'#CDK-003','date'=>'14 Mei 2026','product'=>'Corndog Spicy Cheese',  'qty'=>3,'total'=>'Rp 72.000','status'=>'menunggu',   'img'=>asset('assets/img/CA_DOUBLE_CHEESE.png')],
                ['id'=>'#CDK-004','date'=>'12 Mei 2026','product'=>'Corndog Bulgogi',       'qty'=>1,'total'=>'Rp 25.000','status'=>'dibatalkan', 'img'=>asset('assets/img/CA_RAMEN_MIX.png')],
            ];
            $sc = [
                'selesai'    => ['bg'=>'#DCFCE7','color'=>'#34A853','label'=>'Selesai'],
                'menunggu'   => ['bg'=>'#FEF3C7','color'=>'#D97706','label'=>'Menunggu'],
                'diproses'   => ['bg'=>'#FFEDD5','color'=>'#EA580C','label'=>'Diproses'],
                'dibatalkan' => ['bg'=>'#FCE8E6','color'=>'#EA4335','label'=>'Dibatalkan'],
            ];
            @endphp

            <div id="order-list" class="space-y-4">
                @foreach($orders as $order)
                @php $s = $sc[$order['status']]; @endphp
                <div class="order-row flex items-center gap-4 p-4 rounded-[10px]"
                     data-status="{{ $order['status'] }}"
                     style="border: 1px solid #d9d9d9;">
                    <img src="{{ $order['img'] }}"
                         alt="{{ $order['product'] }}"
                         class="w-14 h-14 rounded-full object-cover flex-none">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm truncate"
                           style="color: var(--color-black);">{{ $order['product'] }}</p>
                        <p class="text-xs mt-0.5" style="color: #9c9c9c;">
                            {{ $order['id'] }} &middot; {{ $order['date'] }} &middot; {{ $order['qty'] }}x item
                        </p>
                    </div>
                    <div class="flex-none flex flex-col items-end gap-1.5">
                        <span class="px-3 py-1 rounded-full text-xs font-bold"
                              style="background-color: {{ $s['bg'] }};
                                     color: {{ $s['color'] }};">
                            {{ $s['label'] }}
                        </span>
                        <p class="font-bold text-sm" style="color: var(--color-black);">
                            {{ $order['total'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Empty state (shown via jQuery when filter has no results) --}}
            <div id="no-orders-msg" style="display: none;"
                 class="py-16 flex flex-col items-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4"
                     fill="none" viewBox="0 0 24 24"
                     stroke="#d9d9d9" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="font-bold text-base mb-1" style="color: var(--color-black);">
                    Tidak ada pesanan
                </p>
                <p class="text-sm" style="color: #9c9c9c;">
                    Belum ada pesanan dengan status ini.
                </p>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center gap-2 mt-8">
                <button class="w-9 h-9 rounded-full font-bold text-sm flex items-center
                               justify-center transition-opacity hover:opacity-70"
                        style="background-color: var(--color-primary); color: white;">1</button>
                <button class="w-9 h-9 rounded-full text-sm flex items-center
                               justify-center transition-opacity hover:opacity-70"
                        style="border: 1px solid #d9d9d9; color: #9c9c9c;">2</button>
                <button class="w-9 h-9 rounded-full text-sm flex items-center
                               justify-center transition-opacity hover:opacity-70"
                        style="border: 1px solid #d9d9d9; color: #9c9c9c;">3</button>
            </div>

        </div>{{-- /.card --}}

    </div>{{-- /#panel-orders --}}

</div>{{-- /.max-w-5xl --}}

@endsection


@push('scripts')
<script>
$(function () {

    /* ═══════════════════════════════════════════════════════════
       TAB SWITCHING — underline style
    ═══════════════════════════════════════════════════════════ */
    $('.profile-tab-btn').on('click', function () {
        var $btn   = $(this);
        var target = $btn.data('target');

        $('.profile-tab-btn').css('color', '#9c9c9c').find('.tab-underline').hide();
        $btn.css('color', 'var(--color-primary)').find('.tab-underline').show();

        $('#panel-profile, #panel-orders').stop(true, true).fadeOut(150, function () {
            $('#' + target).fadeIn(200);
        });
    });

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
       ORDER STATUS FILTER
    ═══════════════════════════════════════════════════════════ */
    var chipDefault = {
        semua:      { bg: 'var(--color-primary)', color: 'white'   },
        menunggu:   { bg: '#FEF3C7',              color: '#D97706' },
        diproses:   { bg: '#FFEDD5',              color: '#EA580C' },
        selesai:    { bg: '#DCFCE7',              color: '#34A853' },
        dibatalkan: { bg: '#FCE8E6',              color: '#EA4335' }
    };

    $('.order-filter-chip').on('click', function () {
        var filter = $(this).data('filter');

        $('.order-filter-chip').each(function () {
            var key = $(this).data('filter');
            var active = (key === filter);
            $(this).css({
                'background-color': active ? 'var(--color-primary)' : chipDefault[key].bg,
                'color'           : active ? 'white'                : chipDefault[key].color
            });
        });

        var visible = 0;
        $('.order-row').each(function () {
            var show = (filter === 'semua' || $(this).data('status') === filter);
            $(this).toggle(show);
            if (show) visible++;
        });
        $('#no-orders-msg').toggle(visible === 0);
    });

    /* ═══════════════════════════════════════════════════════════
       SAVE INFO
    ═══════════════════════════════════════════════════════════ */
    $('#save-info-btn').on('click', function () {
        var name  = $.trim($('#name').val());
        var email = $.trim($('#email').val());

        if (!name || !email) {
            showAlert('Nama dan Email tidak boleh kosong.', 'error');
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

        showAlert('Profil berhasil disimpan.', 'success');
        if (pwOpen) $('#change-pw-panel').slideUp(200);
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
