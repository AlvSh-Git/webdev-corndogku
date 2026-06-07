<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role of this installation
    |--------------------------------------------------------------------------
    | 'local'  = the in-store POS machine (the active sync agent that initiates
    |            every cycle), or
    | 'cpanel' = the online storefront (passive; only answers sync requests).
    */
    'role' => env('SYNC_ROLE', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Rehearsal connection
    |--------------------------------------------------------------------------
    | Local clone of the cPanel database used by `sync:run --driver=db` to test
    | the engine offline. Not used in production.
    */
    'rehearsal_connection' => env('SYNC_REHEARSAL_CONNECTION', 'cpanel_copy'),

    /*
    |--------------------------------------------------------------------------
    | Peer
    |--------------------------------------------------------------------------
    | Base URL of the OTHER side's Sync API, and the shared bearer token used to
    | authenticate the exchange. Only the 'local' role needs the peer URL set,
    | since local drives every cycle over outbound HTTPS.
    */
    'peer' => [
        'name'     => env('SYNC_PEER_NAME', 'cpanel'),
        'base_url' => env('SYNC_PEER_URL'),     // e.g. https://caoimhe.my.id
        'token'    => env('SYNC_TOKEN'),
        'timeout'  => (int) env('SYNC_HTTP_TIMEOUT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tables in scope (business data only)
    |--------------------------------------------------------------------------
    | Order matters: parents must come before children so foreign keys resolve
    | when a batch is applied. Anything not listed here is never synced
    | (sessions, cache, jobs, password_reset_tokens, etc. are deliberately out).
    */
    'tables' => [
        'users',
        'categories',
        'products',
        'components',
        'orders',
        'order_items',
        'payments',
        'chatbot_logs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Conflict resolution
    |--------------------------------------------------------------------------
    | last_write_wins by updated_at. On an exact timestamp tie, 'tie_breaker'
    | decides which side wins deterministically.
    */
    'conflict' => [
        'strategy'    => 'last_write_wins',
        'tie_breaker' => env('SYNC_TIE_BREAKER', 'cpanel'),
    ],

];
