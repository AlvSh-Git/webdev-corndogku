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

];
