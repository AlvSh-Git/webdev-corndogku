<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store Address
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the physical store address. Surfaced through
    | Controller::storeAddress() so the chatbot, receipts, and views stay in
    | sync instead of hardcoding the address in multiple places.
    |
    */

    'address' => env('STORE_ADDRESS', 'Jl. Rungkut Mejoyo Utara No.61, Surabaya'),

    /*
    |--------------------------------------------------------------------------
    | Store Phone / WhatsApp
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the contact number, surfaced through
    | config('store.phone') so the chatbot, footer, and receipts stay in sync.
    |
    */

    'phone' => env('STORE_PHONE', '+62 823-2511-0652'),

];
