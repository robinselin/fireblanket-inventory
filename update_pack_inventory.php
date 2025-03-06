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
use Illuminate\Support\Facades\DB;

try {
    echo "Updating pack inventory based on 2000 total blankets...\n\n";
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    
    echo "Current warehouse quantity: " . number_format($currentQuantity) . " blankets\n\n";
    
    if ($currentQuantity != 2000) {
        echo "Resetting warehouse quantity to exactly 2000 blankets...\n";
        $warehouseService->setWarehouseQuantity(2000, 'Reset to exactly 2000 blankets on ' . date('Y-m-d H:i:s'));
        $currentQuantity = 2000;
    }
    
    // Define pack sizes and calculate available quantities
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100, 150, 250, 500];
    $skuPrefix = 'FB10-2024-';
    
    echo "Calculating available pack quantities with 2000 blankets:\n";
    echo "Pack Size\tSKU\t\tAvailable\tStatus\n";
    echo "-----------------------------------------------------\n";
    
    $inventoryData = [];
    
    foreach ($packSizes as $size) {
        $available = floor($currentQuantity / $size);
        $sku = $skuPrefix . str_pad($size, 3, '0', STR_PAD_LEFT);
        
        // Determine status
        $status = 'In Stock';
        if ($available == 0) {
            $status = 'Out of Stock';
        } elseif ($available <= 5) {
            $status = 'Low Stock';
        }
        
        echo "$size-Pack\t$sku\t$available\t\t$status\n";
        
        $inventoryData[] = [
            'pack_size' => $size,
            'sku' => $sku,
            'available' => $available,
            'status' => $status
        ];
    }
    
    // Update Shopify inventory
    echo "\nUpdating Shopify inventory...\n";
    
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
    
    // Get all products from Shopify
    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $accessToken,
    ])->get("https://$shopDomain/admin/api/$apiVersion/products.json");
    
    if (!$response->successful()) {
        echo "ERROR: Failed to fetch Shopify products: " . $response->body() . "\n";
        exit(1);
    }
    
    $products = $response->json()['products'];
    $updatedCount = 0;
    
    foreach ($products as $product) {
        foreach ($product['variants'] as $variant) {
            // Try to match by SKU first
            $matchedInventory = null;
            foreach ($inventoryData as $inventory) {
                if ($variant['sku'] == $inventory['sku']) {
                    $matchedInventory = $inventory;
                    break;
                }
            }
            
            // If no match by SKU, try to match by title/pack size
            if (!$matchedInventory && preg_match('/(\d+)[-\s]?pack/i', $variant['title'], $matches)) {
                $packSize = (int) $matches[1];
                foreach ($inventoryData as $inventory) {
                    if ($inventory['pack_size'] == $packSize) {
                        $matchedInventory = $inventory;
                        break;
                    }
                }
            }
            
            if ($matchedInventory) {
                $inventoryItemId = $variant['inventory_item_id'];
                $availableQuantity = $matchedInventory['available'];
                
                echo "Updating Shopify inventory for {$matchedInventory['sku']} ({$matchedInventory['pack_size']}-pack): $availableQuantity\n";
                
                $response = Http::withHeaders([
                    'X-Shopify-Access-Token' => $accessToken,
                ])->post("https://$shopDomain/admin/api/$apiVersion/inventory_levels/set.json", [
                    'inventory_item_id' => $inventoryItemId,
                    'location_id' => $locationId,
                    'available' => $availableQuantity
                ]);
                
                if (!$response->successful()) {
                    echo "ERROR: Failed to update Shopify inventory for variant {$variant['id']}: " . $response->body() . "\n";
                    continue;
                }
                
                $updatedCount++;
            }
        }
    }
    
    echo "\nSuccessfully updated $updatedCount Shopify inventory items.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify the warehouse quantity.\n";
    echo "Check your Shopify admin to verify the updated inventory levels for each product variant.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error updating pack inventory: " . $e->getMessage());
}
