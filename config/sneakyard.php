<?php

return [
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@sneakyard.ph'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],
    'seed_demo_catalog' => (bool) env('SEED_DEMO_CATALOG', true),
    'shipping_fee' => (int) env('SNEAKYARD_SHIPPING_FEE', 15000),
    'free_shipping_threshold' => (int) env('SNEAKYARD_FREE_SHIPPING_THRESHOLD', 300000),
    'notifications' => [
        'email_enabled' => (bool) env('ORDER_EMAIL_NOTIFICATIONS', false),
    ],
    'meta' => [
        'graph_version' => env('META_GRAPH_VERSION', 'v24.0'),
        'catalog_id' => env('META_CATALOG_ID'),
        'page_id' => env('META_PAGE_ID'),
        'pixel_id' => env('META_PIXEL_ID'),
        'access_token' => env('META_ACCESS_TOKEN'),
    ],
];
