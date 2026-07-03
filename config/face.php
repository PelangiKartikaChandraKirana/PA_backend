<?php

return [
    // In production, disable server camera streaming endpoints by default.
    'allow_server_camera' => filter_var(
        env('FACE_ALLOW_SERVER_CAMERA', env('APP_ENV', 'production') !== 'production'),
        FILTER_VALIDATE_BOOLEAN
    ),

    // In production, disallow debug_mode bypass by default.
    'allow_debug_bypass' => filter_var(
        env('FACE_ALLOW_DEBUG_BYPASS', env('APP_ENV', 'production') !== 'production'),
        FILTER_VALIDATE_BOOLEAN
    ),

    // Request nonce hardening
    'nonce_ttl_seconds' => (int) env('FACE_NONCE_TTL_SECONDS', 120),

    // Optional request signing (HMAC SHA-256).
    'require_request_signature' => filter_var(
        env('FACE_REQUIRE_REQUEST_SIGNATURE', false),
        FILTER_VALIDATE_BOOLEAN
    ),
    'request_signing_secret' => (string) env('FACE_REQUEST_SIGNING_SECRET', ''),

    // Face service HTTP client
    'service_timeout_seconds' => (int) env('FACE_SERVICE_TIMEOUT_SECONDS', 60),
    'service_connect_timeout_seconds' => (int) env('FACE_SERVICE_CONNECT_TIMEOUT_SECONDS', 5),
];
