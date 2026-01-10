<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure CORS settings for your API. In production, restrict
    | allowed_origins to your actual frontend domain(s).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // In production, replace '*' with your actual frontend domain(s)
    // Example: ['https://yourdomain.com', 'https://www.yourdomain.com']
    'allowed_origins' => env('CORS_ALLOWED_ORIGINS', '*') === '*' 
        ? ['*'] 
        : explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];
