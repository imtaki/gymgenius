<?php

/**
 * Rate Limiting Configuration
 * 
 * Define rate limiting rules for different API endpoints.
 * Format: 'requests_per_minute,window_minutes'
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limit Limits
    |--------------------------------------------------------------------------
    |
    | Define the rate limits for different types of API operations.
    | Format: [limit, window_in_minutes]
    |
    */

    'limits' => [
        // Public endpoints - authentication operations
        'auth_public' => env('RATE_LIMIT_AUTH_PUBLIC', '5,1'),        // 5 requests per minute per IP
        
        // Protected endpoints
        'auth_protected' => env('RATE_LIMIT_AUTH_PROTECTED', '50,1'),   // 50 requests per minute per user
        'read' => env('RATE_LIMIT_READ', '300,1'),                       // 300 requests per minute for read operations
        'write' => env('RATE_LIMIT_WRITE', '30,1'),                      // 30 requests per minute for write operations
        'admin' => env('RATE_LIMIT_ADMIN', '100,1'),                     // 100 requests per minute for admin operations
        'user_data' => env('RATE_LIMIT_USER_DATA', '300,1'),             // 300 requests per minute for user data reads
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Store for Rate Limiting
    |--------------------------------------------------------------------------
    |
    | The cache store used to track rate limit hits. Make sure to use a
    | fast cache store like Redis for production environments.
    |
    */

    'cache_store' => env('RATE_LIMIT_CACHE_STORE', env('CACHE_STORE', 'database')),

    /*
    |--------------------------------------------------------------------------
    | Enable Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Set to false to disable rate limiting (not recommended for production).
    |
    */

    'enabled' => env('RATE_LIMITING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Whitelist IPs/Users
    |--------------------------------------------------------------------------
    |
    | IPs or users in this list will not be rate limited.
    | Example: ['192.168.1.1', 'admin-user-id']
    |
    */

    'whitelist' => explode(',', env('RATE_LIMIT_WHITELIST', '')),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Headers
    |--------------------------------------------------------------------------
    |
    | Whether to include rate limit information in response headers.
    |
    */

    'include_headers' => env('RATE_LIMIT_INCLUDE_HEADERS', true),
];
