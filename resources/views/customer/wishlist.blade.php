@extends('layouts.customer')

@section('title', 'Wishlist Saya - Corndog-Ku')

@section('content')
<div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-16 py-12">
    <h1 class="text-3xl font-bold mb-8">Wishlist Saya ❤️</h1>

    @if($wishlistItems->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border" style="border-color: var(--color-border);">
            <p class="text-gray-500 mb-4">Kamu belum menambahkan produk apa pun ke wishlist.</p>
            <a href="{{ route('menu') }}" class="px-6 py-2.5 rounded-full text-white font-semibold transition-opacity hover:opacity-90" style="background-color: var(--color-primary);">Lihat Menu</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $product)
                <div class="bg-white rounded-2xl p-4 border relative flex flex-col justify-between" style="border-color: var(--color-border); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div>
                        
                        <img src="{{ $product->image }}" alt="{{ $product->name }}">
                        <h3 class="font-bold text-lg mb-1" style="color: var(--color-black);">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($product->description, 60) }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="font-extrabold text-red-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <a href="{{ route('menu') }}" class="inline-flex items-center text-xs font-bold px-4 min-h-[40px] rounded-full text-white transition-opacity hover:opacity-90" style="background-color: var(--color-primary);">Pesan</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection