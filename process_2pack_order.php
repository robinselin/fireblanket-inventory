<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

try {
    echo "Processing new 2-pack order...\n\n";
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    
    // Get current quantity before adjustment
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity before order: " . number_format($currentQuantity) . " blankets\n";
    
    // Process the order - adjust warehouse quantity
    // For a 2-pack, we need to reduce by 2 blankets
    $packSize = 2;
    $quantity = 1; // 1 unit of 2-pack
    $orderNotes = "Processed 2-pack order on " . date('Y-m-d H:i:s');
    
    echo "Adjusting inventory for 1 unit of 2-pack (reducing by 2 blankets)...\n";
    
    // Adjust the warehouse quantity
    $warehouseService->adjustWarehouseQuantity($quantity, $packSize, 'sale');
    
    // Get updated quantity
    $updatedQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Updated warehouse quantity after order: " . number_format($updatedQuantity) . " blankets\n";
    echo "Reduction: " . number_format($currentQuantity - $updatedQuantity) . " blankets\n\n";
    
    // Calculate updated pack quantities
    echo "Updated available pack quantities:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100, 150, 250, 500];
    
    foreach ($packSizes as $size) {
        $available = floor($updatedQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    echo "\nOrder has been processed successfully.\n";
    echo "The Shopify inventory sync job has been automatically dispatched.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify the updated quantities.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error processing 2-pack order: " . $e->getMessage());
}
