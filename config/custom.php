<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Sent on web responses (not the API).  With report_only enabled, the
    | browser logs violations to the console instead of blocking them; use
    | that locally, where Laravel's debug error pages rely on inline scripts.
    |
    | Bootstrap's CSS uses data: SVGs for some icons, so img-src allows data:.
    | Profile avatars come from Gravatar (see App\Support\Gravatar).
    |
    */

    'csp' => [
        'report_only' => (bool) env('CSP_REPORT_ONLY', false),
        'directives' => [
            'default-src'     => ["'self'"],
            'script-src'      => ["'self'", 'https://cdnjs.cloudflare.com'],
            'style-src'       => ["'self'", 'https://cdnjs.cloudflare.com'],
            'font-src'        => ["'self'", 'https://cdnjs.cloudflare.com'],
            'img-src'         => ["'self'", 'data:', 'https://www.gravatar.com'],
            'connect-src'     => ["'self'"],
            'object-src'      => ["'none'"],
            'base-uri'        => ["'self'"],
            'form-action'     => ["'self'"],
            'frame-ancestors' => ["'none'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export API
    |--------------------------------------------------------------------------
    |
    | Bearer token required by GET /api/export.  When empty, the API rejects
    | every request.
    |
    */

    'export_api' => [
        'token' => env('EXPORT_API_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Dump
    |--------------------------------------------------------------------------
    |
    | Configures how data:dump works.
    |
    */

    'dump' => [
        'path' => storage_path('app/private/export'),
        'exports' => [
            [
                'format' => 'csv',
                'type'   => 'simple',
            ],
            [
                'format' => 'json',
                'type'   => 'full',
            ],
            [
                'format' => 'json',
                'type'   => 'simple',
            ],
            [
                'format' => 'yaml',
                'type'   => 'full',
            ],
            [
                'format' => 'yaml',
                'type'   => 'simple',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Throttling
    |--------------------------------------------------------------------------
    |
    | See \Illuminate\Cache\RateLimiting\Limit for details.
    |
    */

    'throttling' => [
        'login' => [
            'ip' => [
                'maxAttempts' => 3,
                'decaySeconds' => 60,
            ],
            'email' => [
                'maxAttempts' => 5,
                'decaySeconds' => 60,
            ],
        ],
    ],

];
