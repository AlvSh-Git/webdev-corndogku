@props([
    'name'        => 'Double Cheese',
    'description' => '',
    'price'       => 'Rp 0',
    'image'       => null,
    'selected'    => false,
])

{{--
    Card container: 268×295px
    The circular image (162px) sits at top-0 and overlaps the card body,
    which starts 29px down. Content padding-top: 148px clears the image overlap.
--}}
<div class="relative w-[268px] h-[295px]">

    {{-- Card body --}}
    <div @class([
        'absolute inset-x-0 bottom-0 top-[29px]',
        'bg-white rounded-[20px] shadow-[3px_4px_20px_0px_rgba(0,0,0,0.25)]',
        'flex flex-col px-[15px] pt-[148px] pb-[17px]',
        'border-2 border-red-blood' => $selected,
    ])>

        {{-- Product name --}}
        <p class="text-red-blood text-xl font-semibold leading-normal">
            {{ $name }}
        </p>

        {{-- Description --}}
        <p class="text-black text-[13px] font-bold leading-normal mt-[7px]">
            {{ $description }}
        </p>

        {{-- Price — pushed to bottom-right --}}
        <p class="text-red-blood text-xl font-semibold leading-normal text-right mt-auto">
            {{ $price }}
        </p>
    </div>

    {{-- Circular food image — floats above the card --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[162px] h-[162px] rounded-full overflow-hidden bg-[#fff8ee]">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                class="w-full h-full object-cover"
            />
        @else
            {{-- Placeholder when no image is provided --}}
            <div class="w-full h-full flex items-center justify-center text-[#d4d4d4]">
                <svg viewBox="0 0 24 24" fill="none" class="w-12 h-12" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                    <path d="M21 15L16 10L9 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 17L11.5 14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        @endif
    </div>

</div>
