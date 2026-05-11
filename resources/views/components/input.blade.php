@props([
    'placeholder' => 'Label',
])

<input
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'type' => 'text',
        'class' => '
            w-full rounded-[13px] bg-white px-4 py-4
            text-base font-normal leading-6 tracking-[0.5px] text-[#212121]
            border border-[#d4d4d4]
            placeholder:text-[#b3b3b3]
            hover:border-2 hover:border-[#d4d4d4]
            [&:not(:placeholder-shown)]:border-[#757575]
            [&:not(:placeholder-shown):hover]:border-2
            [&:not(:placeholder-shown):hover]:border-black
            focus:border-2 focus:border-red-blood focus:outline-none
        ',
    ]) }}
/>
