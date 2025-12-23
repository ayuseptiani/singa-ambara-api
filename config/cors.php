<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // UBAH BARIS INI: Izinkan localhost:3000 akses ke sini
    'allowed_origins' => [
    'https://singa-ambara-suites.web.id',
    'https://www.singa-ambara-suites.web.id',
    'http://localhost:3000', // Biarkan ini agar laptop tetap bisa akses
    ], 

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Ubah jadi true agar login/session aman
];
