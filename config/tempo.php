<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tempo API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for Tempo API requests. Tempo offers regional endpoints:
    | - global: https://api.tempo.io (default)
    | - eu: https://api.eu.tempo.io
    | - us: https://api.us.tempo.io
    |
    */
    'base_url' => env('TEMPO_BASE_URL', 'https://api.tempo.io'),
    'region' => env('TEMPO_REGION', 'global'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The Tempo API version to use. Currently supports version 4.
    |
    */
    'api_version' => env('TEMPO_API_VERSION', '4'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Tempo API authentication method.
    |
    | SECURITY WARNING: For OAuth2, tokens are stored in .env file.
    | In production shared server environments, consider using a more secure
    | token storage solution (database with encryption, vault service, etc.).
    | This is suitable for private servers and development environments only.
    |
    */
    'auth' => [
        'type' => env('TEMPO_AUTH_TYPE', 'token'), // 'token' or 'oauth2'

        'token' => [
            'bearer' => env('TEMPO_TOKEN'),
        ],

        'oauth2' => [
            'client_id' => env('TEMPO_CLIENT_ID'),
            'client_secret' => env('TEMPO_CLIENT_SECRET'),
            'refresh_disabled' => env('TEMPO_OAUTH_REFRESH_DISABLED', false),

            // OAuth2 token endpoints
            'authorize_url' => 'https://api.tempo.io/oauth/authorize/redirect',
            'token_url' => 'https://api.tempo.io/oauth/token/',
            'revoke_url' => 'https://api.tempo.io/oauth/revoke_token/',
            'token_ttl' => 5184000, // 60 days in seconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Configure HTTP client behavior for API requests.
    |
    */
    'http' => [
        'timeout' => env('TEMPO_TIMEOUT', 30),
        'connect_timeout' => 10,

        'retry' => [
            'times' => env('TEMPO_RETRY_TIMES', 3),
            'sleep' => env('TEMPO_RETRY_SLEEP', 1000), // milliseconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Enable optional built-in caching for read-only endpoints.
    | Cache keys include hashed OAuth2 token and account scope to prevent
    | data collision between different authenticated users/accounts.
    |
    | SECURITY WARNING: Cache keys include token info. Ensure your cache
    | backend (Redis, Memcached) is properly secured in shared environments.
    |
    */
    'cache' => [
        'enabled' => env('TEMPO_CACHE_ENABLED', false),
        'default_ttl' => env('TEMPO_CACHE_TTL', 3600),

        // Per-endpoint TTL (in seconds)
        'endpoints' => [
            'teams' => 3600,
            'roles' => 3600,
            'projects' => 1800,
            'global_configuration' => 7200,
            'periods' => 7200,
            'holiday_schemes' => 3600,
            'workload_schemes' => 3600,
            'accounts' => 1800,
            'customers' => 1800,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook handling for Tempo events.
    |
    */
    'webhooks' => [
        'signature_secret' => env('TEMPO_WEBHOOK_SECRET'),
        'route_path' => env('TEMPO_WEBHOOK_ROUTE', '/api/webhooks/tempo'),
        'route_name' => 'tempo.webhook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for API requests and responses.
    |
    */
    'logging' => [
        'enabled' => env('TEMPO_LOG_ENABLED', false),
        'channel' => env('TEMPO_LOG_CHANNEL', 'single'),
        'level' => env('TEMPO_LOG_LEVEL', 'info'),
    ],
];
