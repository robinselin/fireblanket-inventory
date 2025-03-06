<?php

return [
    'api_key' => env('SHOPIFY_API_KEY'),
    'access_token' => env('SHOPIFY_ACCESS_TOKEN'),
    'shop_domain' => env('SHOPIFY_SHOP_DOMAIN'),
    'api_version' => env('SHOPIFY_API_VERSION', '2024-01'),
    'location_id' => env('SHOPIFY_LOCATION_ID'),
];
