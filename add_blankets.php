<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use App\Models\WarehouseQuantity;
use Illuminate\Support\Facades\Log;

try {
    // Get the current warehouse quantity
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity: " . number_format($currentQuantity) . " blankets\n";
    
    // Add 2000 blankets
    $blankets_to_add = 2000;
    $warehouseService->adjustWarehouseQuantity($blankets_to_add, 1, 'restock');
    
    // Get the updated quantity
    $updatedQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Updated warehouse quantity: " . number_format($updatedQuantity) . " blankets (added {$blankets_to_add})\n";
    
    // Calculate available pack quantities
    echo "\nUpdated Available Pack Quantities:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
    foreach ($packSizes as $size) {
        $available = floor($updatedQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    echo "\nThe database has been updated. Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    echo "Note: If Shopify sync fails, you may need to update your API token with write_inventory permissions.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error adding blankets: " . $e->getMessage());
}
