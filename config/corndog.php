<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Corndog preview images
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the layered custom-corndog thumbnail used by
    | the customer order history and the cashier/owner order-detail drawers.
    | Keys are the uppercase component names stored in order_items.custom_notes.
    |
    */

    'varian_images' => [
        'ORIGINAL' => 'assets/img/custom_original.png',
        'POTATO'   => 'assets/img/custom_potato.png',
        'RAMEN'    => 'assets/img/custom_ramen.png',
    ],

    'sauce_images' => [
        'KETCHUP'      => 'assets/img/custom_ketchup.png',
        'MAYONNAISE'   => 'assets/img/custom_mayonnaise.png',
        'HOT SAUCE'    => 'assets/img/custom_hotsauce.png',
        'CHEESE SAUCE' => 'assets/img/custom_cheesesauce.png',
    ],

    // Fallback when a varian has no mapped image.
    'fallback_image' => 'assets/img/CA_ORIGINAL.png',

];
