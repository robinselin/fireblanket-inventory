<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use App\Models\WarehouseQuantity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    echo "Simulating receipt of new shipment of 2000 blankets...\n";
    
    // First, clean up the database by keeping only one record
    echo "Cleaning up warehouse_quantity table...\n";
    
    // Keep only the first record and delete others
    $firstRecord = DB::table('warehouse_quantity')->first();
    if ($firstRecord) {
        DB::table('warehouse_quantity')->where('id', '!=', $firstRecord->id)->delete();
        echo "Kept record ID: " . $firstRecord->id . " and deleted others.\n";
    } else {
        // Create a new record if none exists
        DB::table('warehouse_quantity')->insert([
            'total_quantity' => 0,
            'notes' => 'Initial record',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Created new warehouse_quantity record.\n";
    }
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    
    // Get current quantity (should be 0)
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity before shipment: " . number_format($currentQuantity) . " blankets\n";
    
    // Add 2000 blankets
    echo "Adding 2000 blankets from new shipment...\n";
    $warehouseService->setWarehouseQuantity(2000, 'Received shipment of 2000 blankets on ' . date('Y-m-d'));
    
    // Verify the update
    $updatedQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Updated warehouse quantity: " . number_format($updatedQuantity) . " blankets\n";
    
    // Calculate available pack quantities
    echo "\nAvailable Pack Quantities with new shipment:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
    foreach ($packSizes as $size) {
        $available = floor($updatedQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    echo "\nShipment has been received and inventory updated to 2000 blankets.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    
    // Trigger Shopify sync
    echo "\nTriggering Shopify inventory sync...\n";
    dispatch(new App\Jobs\UpdateShopifyInventoryJob());
    echo "Shopify sync job dispatched. Note: This requires the write_inventory permission in your Shopify API token.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error processing new shipment: " . $e->getMessage());
}
