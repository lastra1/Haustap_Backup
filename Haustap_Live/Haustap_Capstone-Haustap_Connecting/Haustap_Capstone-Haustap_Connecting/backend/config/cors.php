<?php

return [
    'paths' => ['api/*', 'auth/*', 'sso/*', 'admin/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8001',
        'http://localhost:8003',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

