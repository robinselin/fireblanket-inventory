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
    echo "Updating warehouse inventory to 2000 blankets...\n";
    
    // First, clean up the database by keeping only one record
    echo "Cleaning up warehouse_quantity table...\n";
    
    // Get the first record
    $firstRecord = DB::table('warehouse_quantity')->first();
    
    if ($firstRecord) {
        // Delete all other records
        $deleted = DB::table('warehouse_quantity')->where('id', '!=', $firstRecord->id)->delete();
        echo "Kept record ID: " . $firstRecord->id . " and deleted " . $deleted . " other records.\n";
        
        // Update the first record to 2000 blankets
        DB::table('warehouse_quantity')->where('id', $firstRecord->id)->update([
            'total_quantity' => 2000,
            'notes' => 'Initial inventory of 2000 blankets set on ' . date('Y-m-d'),
            'updated_at' => now()
        ]);
    } else {
        // Create a new record if none exists
        DB::table('warehouse_quantity')->insert([
            'total_quantity' => 2000,
            'notes' => 'Initial inventory of 2000 blankets set on ' . date('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Created new warehouse_quantity record with 2000 blankets.\n";
    }
    
    // Verify the update
    $records = DB::table('warehouse_quantity')->get();
    
    echo "\nVerifying update:\n";
    foreach ($records as $record) {
        echo "ID: " . $record->id . ", Quantity: " . number_format($record->total_quantity) . ", Notes: " . ($record->notes ?? 'None') . "\n";
    }
    
    // Get the warehouse service to verify
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "\nWarehouseService::getCurrentWarehouseQuantity() returns: " . number_format($currentQuantity) . " blankets\n";
    
    // Calculate available pack quantities
    echo "\nAvailable Pack Quantities with 2000 blankets:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
    foreach ($packSizes as $size) {
        $available = floor($currentQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    echo "\nInventory has been updated to 2000 blankets.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    
    // Trigger Shopify sync
    echo "\nTriggering Shopify inventory sync...\n";
    dispatch(new App\Jobs\UpdateShopifyInventoryJob());
    echo "Shopify sync job dispatched. Note: This requires the write_inventory permission in your Shopify API token.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error updating to 2000 blankets: " . $e->getMessage());
}
