<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // Added '*' for safety

    'allowed_methods' => ['*'], // Allow POST, GET, OPTIONS, etc.

    'allowed_origins' => ['*'], // Allow localhost, 127.0.0.1, etc.

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];