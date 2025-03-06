<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

try {
    echo "Checking synchronization status between local database and Shopify...\n\n";
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    
    echo "Local warehouse quantity: " . number_format($currentQuantity) . " blankets\n\n";
    
    // Get Shopify API credentials
    $shopDomain = config('shopify.shop_domain');
    $apiVersion = config('shopify.api_version');
    $accessToken = config('shopify.access_token');
    $locationId = config('shopify.location_id');
    
    if (empty($accessToken) || empty($shopDomain) || empty($locationId)) {
        echo "ERROR: Missing Shopify API credentials. Please check your .env file.\n";
        echo "Required: SHOPIFY_ACCESS_TOKEN, SHOPIFY_SHOP_DOMAIN, SHOPIFY_LOCATION_ID\n";
        exit(1);
    }
    
    echo "Shopify API Configuration:\n";
    echo "- Shop Domain: $shopDomain\n";
    echo "- API Version: $apiVersion\n";
    echo "- Location ID: $locationId\n\n";
    
    // Get all products from Shopify
    echo "Fetching inventory data from Shopify...\n";
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $accessToken,
    ])->get("https://$shopDomain/admin/api/$apiVersion/products.json");
    
    if (!$response->successful()) {
        echo "ERROR: Failed to fetch Shopify products: " . $response->body() . "\n";
        exit(1);
    }
    
    $products = $response->json()['products'];
    
    // Define pack sizes and calculate expected quantities
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100, 150, 250, 500];
    $skuPrefix = 'FB10-2024-';
    
    echo "Comparing local inventory with Shopify inventory:\n";
    echo "Pack Size\tSKU\t\tLocal\tShopify\tStatus\n";
    echo "-----------------------------------------------------\n";
    
    $syncStatus = true;
    $inventoryItems = [];
    
    // Calculate expected quantities
    foreach ($packSizes as $size) {
        $expectedQuantity = floor($currentQuantity / $size);
        $sku = $skuPrefix . str_pad($size, 3, '0', STR_PAD_LEFT);
        $inventoryItems[$sku] = [
            'pack_size' => $size,
            'expected' => $expectedQuantity,
            'actual' => null,
            'synced' => false
        ];
    }
    
    // Check Shopify inventory
    foreach ($products as $product) {
        foreach ($product['variants'] as $variant) {
            if (!empty($variant['sku']) && isset($inventoryItems[$variant['sku']])) {
                // Get inventory level for this variant
                $inventoryItemId = $variant['inventory_item_id'];
                $inventoryResponse = Http::withHeaders([
                    'X-Shopify-Access-Token' => $accessToken,
                ])->get("https://$shopDomain/admin/api/$apiVersion/inventory_levels.json", [
                    'inventory_item_ids' => $inventoryItemId,
                    'location_ids' => $locationId
                ]);
                
                if ($inventoryResponse->successful() && !empty($inventoryResponse->json()['inventory_levels'])) {
                    $shopifyQuantity = $inventoryResponse->json()['inventory_levels'][0]['available'];
                    $inventoryItems[$variant['sku']]['actual'] = $shopifyQuantity;
                    $inventoryItems[$variant['sku']]['synced'] = ($shopifyQuantity == $inventoryItems[$variant['sku']]['expected']);
                    
                    if (!$inventoryItems[$variant['sku']]['synced']) {
                        $syncStatus = false;
                    }
                }
            }
        }
    }
    
    // Display results
    foreach ($inventoryItems as $sku => $item) {
        $status = $item['actual'] === null ? 'Not Found' : ($item['synced'] ? 'Synced' : 'Not Synced');
        $actualQuantity = $item['actual'] === null ? 'N/A' : $item['actual'];
        echo "{$item['pack_size']}-Pack\t$sku\t{$item['expected']}\t$actualQuantity\t$status\n";
    }
    
    echo "\nOverall Synchronization Status: " . ($syncStatus ? "SYNCED ✓" : "NOT SYNCED ✗") . "\n";
    
    if (!$syncStatus) {
        echo "\nRecommendation: Run the following command to force a sync:\n";
        echo "php artisan queue:work --once\n";
        echo "OR\n";
        echo "php update_pack_inventory.php\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error checking sync status: " . $e->getMessage());
}
