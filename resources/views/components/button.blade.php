@props([
    'variant' => 'primary',
])

@php
    $base = 'flex items-center justify-center gap-2 overflow-hidden rounded-lg px-3 py-3 w-full text-base font-normal leading-none whitespace-nowrap transition-opacity hover:opacity-90';

    $variants = [
        'primary' => 'bg-[#c00f0c] border border-[#2c2c2c] text-[#f5f5f5]',
        'outline' => 'bg-white border-[1.5px] border-[#2c2c2c] text-black',
    ];
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $base . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>
