@extends('layouts.app')

@section('title', 'Jadwal Operasional')

@section('content')
@php
    $isOpen     = $storeInfo['is_open'] ?? true;
    $reason     = $storeInfo['reason']     ?? 'schedule';
    $reopenDay  = $storeInfo['reopen_day']  ?? '';
    $reopenTime = $storeInfo['reopen_time'] ?? '';
    $isManual   = ($reason === 'manual');
@endphp

<style>
    /* The native <input type="time"> renders its own clock/picker icon,
       which collided with the custom clock SVG → two clock icons per
       field (most obvious on mobile). Hide the native indicator but keep
       it as an invisible, clickable overlay on the right so tapping the
       custom clock still opens the time picker. */
    .buka-input::-webkit-calendar-picker-indicator,
    .tutup-input::-webkit-calendar-picker-indicator {
        position: absolute;
        top: 0;
        right: 0;
        width: 2rem;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }
    /* Firefox / others: hide any spin controls so the field stays clean. */
    .buka-input::-moz-clear,
    .tutup-input::-moz-clear { display: none; }
</style>

{{-- Full-page wrapper: cream background + decorative blobs --}}
<div class="relative overflow-hidden -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8" style="background-color:#fffef0;min-height:calc(100vh - 4rem);">

    {{-- Decorative background blobs --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
        <div class="absolute rounded-full"
             style="width:215px;height:215px;left:-80px;top:140px;background-color:#ffd97a;opacity:.65;"></div>
        <div class="absolute rounded-full"
             style="width:215px;height:215px;right:-40px;top:-30px;background-color:#ffd97a;opacity:.55;"></div>
        <div class="absolute rounded-full"
             style="width:215px;height:215px;right:180px;bottom:40px;background-color:#f6a623;opacity:.40;"></div>
        <div class="absolute rounded-full"
             style="width:215px;height:215px;right:-55px;top:520px;background-color:#ffd97a;opacity:.50;"></div>
        <div class="absolute rounded-full" style="width:21px;height:21px;left:48px;top:505px;background-color:#fbbf24;"></div>
        <div class="absolute rounded-full" style="width:21px;height:21px;right:58px;top:210px;background-color:#fbbf24;"></div>
        <div class="absolute rounded-full" style="width:15px;height:15px;right:215px;top:22px;background-color:#f59e0b;"></div>
        <div class="absolute rounded-full" style="width:15px;height:15px;left:100px;top:252px;background-color:#f59e0b;"></div>
        <div class="absolute rounded-full" style="width:14px;height:14px;left:45px;top:148px;background-color:#fde68a;"></div>
        <div class="absolute rounded-full" style="width:15px;height:15px;left:12px;top:370px;background-color:#fbbf24;"></div>
        <div class="absolute rounded-full" style="width:15px;height:15px;right:12px;top:530px;background-color:#f59e0b;"></div>
    </div>

    {{-- Page header: title + 3D storefront illustration --}}
    <div class="relative z-10 flex items-start justify-between mb-6">
        <div>
            <h1 class="font-bold text-gray-900 leading-tight"
                style="font-size:clamp(1.5rem,3vw,2.25rem);">Jadwal Operasional Toko</h1>
            <p class="text-gray-500 mt-1 text-sm lg:text-base">Atur hari dan jam buka tutup toko secara rutin</p>
        </div>
    </div>

    {{-- Two-panel layout --}}
    <div class="relative z-10 flex flex-col lg:flex-row gap-6 items-start">

        {{-- LEFT — Schedule Table Card --}}
        <div class="flex-1 min-w-0 bg-white rounded-3xl shadow-md">
            <form method="POST" action="{{ route('owner.jadwal.save') }}">
                @csrf

                {{-- Schedule rows — stacked on mobile, table-like grid on md+ --}}
                <div class="m-4 sm:m-6 border border-gray-200 rounded-2xl overflow-hidden">
                    {{-- Column headers (desktop only) --}}
                    <div class="hidden md:grid md:grid-cols-[5.5rem_9rem_1fr_1fr] md:items-center md:gap-2 px-4 py-3"
                         style="background-color:#fff8e4;border-bottom:1px solid #e5e7eb;">
                        <div class="text-sm font-bold text-gray-800">Hari</div>
                        <div class="text-sm font-bold text-gray-800">Status</div>
                        <div class="text-sm font-bold text-gray-800 text-center">Jam Buka</div>
                        <div class="text-sm font-bold text-gray-800 text-center">Jam Tutup</div>
                    </div>

                    @foreach ($schedule as $i => $day)
                    @php $dayOpen = $day['open'] ?? false; @endphp
                    <div class="jadwal-row px-4 py-3 md:grid md:grid-cols-[5.5rem_9rem_1fr_1fr] md:items-center md:gap-2"
                         data-day="{{ $day['key'] }}"
                         style="{{ $i < count($schedule) - 1 ? 'border-bottom:1px solid #f3f4f6;' : '' }}">

                        {{-- Day name + status toggle row (mobile: stacked on its own row with toggle inline) --}}
                        <div class="flex items-center justify-between md:contents">
                            <span class="text-sm font-bold text-gray-800">{{ $day['label'] }}</span>

                            <div class="flex items-center gap-2">
                                <input type="checkbox"
                                       name="open_{{ $day['key'] }}"
                                       class="sr-only day-toggle"
                                       {{ $dayOpen ? 'checked' : '' }}>
                                <div class="toggle-pill relative cursor-pointer"
                                     style="width:40px;height:20px;border-radius:9999px;flex-shrink:0;transition:background-color .2s;background-color:{{ $dayOpen ? '#930c0e' : '#d1d5db' }};">
                                    <div class="toggle-knob absolute bg-white rounded-full"
                                         style="width:16px;height:16px;top:2px;transition:left .2s;left:{{ $dayOpen ? '22px' : '2px' }};border:1px solid {{ $dayOpen ? 'white' : '#d1d5db' }};"></div>
                                </div>
                                <span class="toggle-label-text text-sm font-medium {{ $dayOpen ? 'text-gray-700' : 'text-red-500' }}">
                                    {{ $dayOpen ? 'Buka' : 'Tutup' }}
                                </span>
                            </div>
                        </div>

                        {{-- Time inputs: side by side on mobile, separate cells on md+ --}}
                        <div class="grid grid-cols-2 gap-2 mt-3 md:mt-0 md:contents">
                            <div class="md:flex md:items-center md:gap-2">
                                <label class="block md:hidden text-[11px] font-semibold text-gray-500 mb-1">Jam Buka</label>
                                <div class="relative md:flex-1">
                                    <input type="time"
                                           name="buka_{{ $day['key'] }}"
                                           class="buka-input w-full border rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-1 focus:ring-red-800 transition-colors"
                                           style="padding-right:2rem;border-color:#e5e7eb;background-color:{{ $dayOpen ? 'white' : '#f3f4f6' }};color:{{ $dayOpen ? '#1e293b' : '#9ca3af' }};"
                                           value="{{ $dayOpen ? ($day['buka'] ?? '') : '' }}"
                                           {{ !$dayOpen ? 'disabled' : '' }}>
                                    <span class="clock-icon absolute top-1/2 -translate-y-1/2 pointer-events-none"
                                          style="right:7px;color:{{ $dayOpen ? '#9ca3af' : '#d1d5db' }};">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                </div>
                                <span class="hidden md:inline text-base select-none flex-none" style="color:#d1d5db;">-</span>
                            </div>

                            <div>
                                <label class="block md:hidden text-[11px] font-semibold text-gray-500 mb-1">Jam Tutup</label>
                                <div class="relative">
                                    <input type="time"
                                           name="tutup_{{ $day['key'] }}"
                                           class="tutup-input w-full border rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-1 focus:ring-red-800 transition-colors"
                                           style="padding-right:2rem;border-color:#e5e7eb;background-color:{{ $dayOpen ? 'white' : '#f3f4f6' }};color:{{ $dayOpen ? '#1e293b' : '#9ca3af' }};"
                                           value="{{ $dayOpen ? ($day['tutup'] ?? '') : '' }}"
                                           {{ !$dayOpen ? 'disabled' : '' }}>
                                    <span class="clock-icon absolute top-1/2 -translate-y-1/2 pointer-events-none"
                                          style="right:7px;color:{{ $dayOpen ? '#9ca3af' : '#d1d5db' }};">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Info banner --}}
                <div class="mx-6 mb-4 flex items-center gap-3 rounded-xl px-4 py-3"
                     style="background-color:#fff9ea;border:1px solid #fde68a;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-none" style="width:20px;height:20px;color:#d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="color:#9a7d3f;font-size:12px;">Jadwal ini akan diterapkan setiap hari sesuai pengaturan di atas.</span>
                </div>

                {{-- Save button — bottom-right --}}
                <div class="px-6 pb-6 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 font-bold text-white text-sm rounded-xl px-6 py-3 hover:opacity-90 transition-opacity"
                            style="background-color:#930c0e;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT — Manual Override Card --}}
        <div class="w-full lg:w-72 xl:w-80 flex-none">
            <div class="bg-white rounded-3xl p-6 flex flex-col gap-4"
                 style="box-shadow:3px 4px 20px rgba(0,0,0,0.07);">

                {{-- Heading --}}
                <div>
                    <h2 class="font-bold text-gray-800" style="font-size:15px;">Buka Tutup Manual (Saat Ini)</h2>
                    <p style="color:#6b7280;font-size:10px;margin-top:2px;">Anda dapat menutup atau membuka toko kapan saja.</p>
                </div>

                {{-- Status box --}}
                <div id="status-box"
                     class="rounded-2xl border flex items-center justify-between gap-2 px-4 py-3"
                     style="background-color:{{ $isOpen ? '#f2fcf2' : '#fff6f6' }};border-color:{{ $isOpen ? '#dcfce7' : '#fecaca' }};">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div id="status-dot" class="rounded-full flex-none"
                             style="width:16px;height:16px;background-color:{{ $isOpen ? '#22c55e' : '#ef4444' }};"></div>
                        <div class="min-w-0">
                            <div id="status-label"
                                 class="font-bold uppercase leading-none"
                                 style="font-size:16px;letter-spacing:.5px;color:{{ $isOpen ? '#166534' : '#991b1b' }};">
                                @if ($isOpen)
                                    TOKO BUKA
                                @elseif ($isManual)
                                    TOKO TUTUP MANUAL
                                @else
                                    TOKO TUTUP
                                @endif
                            </div>
                            <div id="status-sub"
                                 style="color:#4b5563;font-size:14px;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                @if ($isOpen && $todayLabel)
                                    {{ $todayLabel }}{{ $todayBuka ? ', ' . $todayBuka . ' WIB' : '' }}
                                @elseif (!$isOpen && ($reopenDay || $reopenTime))
                                    Buka: {{ $reopenDay }}{{ $reopenTime ? ', ' . $reopenTime . ' WIB' : '' }}
                                @else
                                    &nbsp;
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Corndog-Ku logo (same asset as the home view) --}}
                    <img src="{{ asset('assets/img/logo.png') }}"
                         onerror="this.style.display='none'"
                         alt="Corndog-Ku" id="status-illus"
                         class="rounded-full object-cover"
                         style="width:64px;height:64px;flex-shrink:0;">
                </div>

                {{-- Buka Sekarang (outline green + unlock icon) --}}
                <button id="btn-buka"
                        class="w-full flex items-center justify-center gap-2 font-bold text-sm rounded-xl px-4 py-2.5 transition-opacity"
                        style="background-color:#effff5;border:2px solid #15803d;color:#15803d;{{ $isOpen ? 'opacity:.5;cursor:not-allowed;' : 'cursor:pointer;' }}"
                        {{ $isOpen ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    Buka Sekarang
                </button>

                {{-- Tutup Sekarang (outline red + lock icon) --}}
                <button id="btn-tutup"
                        class="w-full flex items-center justify-center gap-2 font-bold text-sm rounded-xl px-4 py-2.5 transition-opacity"
                        style="background-color:#fff6f6;border:2px solid #ef4444;color:#ef4444;{{ !$isOpen ? 'opacity:.5;cursor:not-allowed;' : 'cursor:pointer;' }}"
                        {{ !$isOpen ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Tutup Sekarang
                </button>

                {{-- Warning banner --}}
                <div class="flex items-start gap-2 rounded-xl px-3 py-3"
                     style="background-color:#fff9ea;border:1px solid #fde68a;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-none" style="width:20px;height:20px;color:#d97706;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span style="color:#9a7d3f;font-size:10px;line-height:1.6;">Jika toko ditutup manual, pelanggan tidak dapat memesan sampai Anda buka kembali.</span>
                </div>

            </div>
        </div>

    </div>{{-- /two-panel --}}

    {{-- Save success flash --}}
    @if (session('saved'))
    <div id="flash-saved"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3 rounded-2xl text-white text-sm font-semibold shadow-xl"
         style="background-color:#166534;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Jadwal berhasil disimpan!
    </div>
    <script>setTimeout(function(){ $('#flash-saved').fadeOut(400); }, 3000);</script>
    @endif

</div>{{-- /page wrapper --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
$(function () {

    // PHP-rendered state strings for re-use in JS status updates
    var todayLabel = @json($todayLabel);
    var todayBuka  = @json($todayBuka);

    //  Toggle pill click 
    $(document).on('click', '.toggle-pill', function () {
        var $row = $(this).closest('.jadwal-row');
        var $cb  = $row.find('.day-toggle');
        $cb.prop('checked', !$cb.prop('checked')).trigger('change');
    });

    //  Toggle state change 
    $(document).on('change', '.day-toggle', function () {
        var $row      = $(this).closest('.jadwal-row');
        var isChecked = $(this).is(':checked');
        var $pill     = $row.find('.toggle-pill');
        var $knob     = $row.find('.toggle-knob');
        var $txt      = $row.find('.toggle-label-text');
        var $buka     = $row.find('.buka-input');
        var $tutup    = $row.find('.tutup-input');
        var $clocks   = $row.find('.clock-icon');

        if (isChecked) {
            $pill.css('background-color', '#930c0e');
            $knob.css({ left: '22px', 'border-color': 'white' });
            $txt.text('Buka').css('color', '#374151');

            $buka.val($buka.data('saved') || '10:00')
                 .prop('disabled', false)
                 .css({ 'background-color': 'white', color: '#1e293b' });
            $tutup.val($tutup.data('saved') || '21:00')
                  .prop('disabled', false)
                  .css({ 'background-color': 'white', color: '#1e293b' });
            $clocks.css('color', '#9ca3af');
        } else {
            $pill.css('background-color', '#d1d5db');
            $knob.css({ left: '2px', 'border-color': '#d1d5db' });
            $txt.text('Tutup').css('color', '#ef4444');

            $buka.data('saved', $buka.val()).val('').prop('disabled', true)
                 .css({ 'background-color': '#f3f4f6', color: '#9ca3af' });
            $tutup.data('saved', $tutup.val()).val('').prop('disabled', true)
                  .css({ 'background-color': '#f3f4f6', color: '#9ca3af' });
            $clocks.css('color', '#d1d5db');
        }
    });

    //  Update right-panel status UI after AJAX 
    function updateStatusUI(info) {
        var isOpen   = info.is_open;
        var isManual = (info.reason === 'manual');

        $('#status-box').css({
            'background-color': isOpen ? '#f2fcf2' : '#fff6f6',
            'border-color':     isOpen ? '#dcfce7' : '#fecaca',
        });
        $('#status-dot').css('background-color', isOpen ? '#22c55e' : '#ef4444');

        var label = isOpen ? 'TOKO BUKA' : (isManual ? 'TOKO TUTUP MANUAL' : 'TOKO TUTUP');
        $('#status-label').text(label).css('color', isOpen ? '#166534' : '#991b1b');

        if (isOpen && todayLabel) {
            $('#status-sub').text(todayLabel + (todayBuka ? ', ' + todayBuka + ' WIB' : ''));
        } else if (!isOpen && (info.reopen_day || info.reopen_time)) {
            $('#status-sub').text('Buka: ' + (info.reopen_day || '') + (info.reopen_time ? ', ' + info.reopen_time + ' WIB' : ''));
        } else {
            $('#status-sub').html('&nbsp;');
        }

        if (isOpen) {
            $('#btn-buka').prop('disabled', true).css({ opacity: .5, cursor: 'not-allowed' });
            $('#btn-tutup').prop('disabled', false).css({ opacity: 1, cursor: 'pointer' });
        } else {
            $('#btn-buka').prop('disabled', false).css({ opacity: 1, cursor: 'pointer' });
            $('#btn-tutup').prop('disabled', true).css({ opacity: .5, cursor: 'not-allowed' });
        }
    }

    //  Buka Sekarang 
    $('#btn-buka').on('click', function () {
        if ($(this).prop('disabled')) return;

        Swal.fire({
            title:              'Buka Toko Sekarang?',
            text:               'Toko akan kembali buka dan pelanggan dapat melakukan pemesanan.',
            icon:               'question',
            showCancelButton:   true,
            confirmButtonText:  'Ya, Buka Sekarang',
            cancelButtonText:   'Batal',
            confirmButtonColor: '#15803d',
            cancelButtonColor:  '#6b7280',
            reverseButtons:     true,
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.post('{{ route("owner.jadwal.toggle") }}', {
                _token: '{{ csrf_token() }}',
                status: 'available',
            })
            .done(function (res) {
                updateStatusUI(res.store_info);
                Swal.fire({ icon: 'success', title: 'Toko Dibuka!', text: 'Pelanggan kini dapat memesan.', timer: 2000, showConfirmButton: false });
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan, silakan coba lagi.' });
            });
        });
    });

    //  Tutup Sekarang — Step 1: choose reopen day & time 
    $('#btn-tutup').on('click', function () {
        if ($(this).prop('disabled')) return;

        Swal.fire({
            title: 'Tutup Toko Sekarang',
            html:
                '<p style="color:#6b7280;font-size:13px;margin-bottom:16px;">Toko akan ditutup. Pilih kapan toko dibuka kembali:</p>'
              + '<div style="text-align:left;display:flex;flex-direction:column;gap:12px;">'
              +   '<div>'
              +     '<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">Hari Buka Kembali</label>'
              +     '<select id="swal-day" style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;color:#1f2937;background:white;outline:none;">'
              +       '<option value="">-- Pilih Hari --</option>'
              +       '<option value="Besok">Besok</option>'
              +       '<option value="Senin">Senin</option>'
              +       '<option value="Selasa">Selasa</option>'
              +       '<option value="Rabu">Rabu</option>'
              +       '<option value="Kamis">Kamis</option>'
              +       '<option value="Jumat">Jumat</option>'
              +       '<option value="Sabtu">Sabtu</option>'
              +       '<option value="Minggu">Minggu</option>'
              +     '</select>'
              +   '</div>'
              +   '<div>'
              +     '<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">Jam Buka Kembali</label>'
              +     '<input type="time" id="swal-time" value="10:00"'
              +       ' style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;color:#1f2937;outline:none;box-sizing:border-box;">'
              +   '</div>'
              + '</div>',
            icon:               'warning',
            showCancelButton:   true,
            confirmButtonText:  'Lanjut →',
            cancelButtonText:   'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor:  '#6b7280',
            focusConfirm: false,
            preConfirm: function () {
                var day  = document.getElementById('swal-day').value;
                var time = document.getElementById('swal-time').value;
                if (!day)  { Swal.showValidationMessage('Harap pilih hari buka kembali.'); return false; }
                if (!time) { Swal.showValidationMessage('Harap pilih jam buka kembali.'); return false; }
                return { day: day, time: time };
            },
        }).then(function (res1) {
            if (!res1.isConfirmed || !res1.value) return;

            var chosenDay  = res1.value.day;
            var chosenTime = res1.value.time;

            // Step 2: Confirmation
            Swal.fire({
                title: 'Konfirmasi Penutupan',
                html:
                    '<div style="font-size:14px;color:#4b5563;line-height:1.6;">'
                  +   '<p>Anda akan <strong style="color:#dc2626;">menutup toko sekarang</strong> dan membukanya kembali pada:</p>'
                  +   '<p style="font-size:18px;font-weight:700;color:#1f2937;margin:12px 0;">'
                  +     chosenDay + ', pukul ' + chosenTime + ' WIB'
                  +   '</p>'
                  +   '<p style="font-size:12px;color:#b45309;background:#fef3c7;border-radius:8px;padding:8px 12px;">'
                  +     '⚠️ Pelanggan tidak dapat memesan selama toko ditutup.'
                  +   '</p>'
                  + '</div>',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonText:  'Ya, Tutup Sekarang',
                cancelButtonText:   'Kembali',
                confirmButtonColor: '#ef4444',
                cancelButtonColor:  '#6b7280',
                reverseButtons:     true,
            }).then(function (res2) {
                if (!res2.isConfirmed) return;

                $.post('{{ route("owner.jadwal.toggle") }}', {
                    _token:      '{{ csrf_token() }}',
                    status:      'unavailable',
                    reopen_day:  chosenDay,
                    reopen_time: chosenTime,
                })
                .done(function (res) {
                    updateStatusUI(res.store_info);
                    Swal.fire({
                        icon:  'success',
                        title: 'Toko Ditutup!',
                        text:  'Akan buka kembali ' + chosenDay + ' pukul ' + chosenTime + ' WIB.',
                        timer: 2500,
                        showConfirmButton: false,
                    });
                })
                .fail(function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Terjadi kesalahan, silakan coba lagi.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                });
            });
        });
    });

});
</script>
@endpush
