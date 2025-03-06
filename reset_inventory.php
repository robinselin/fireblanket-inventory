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
use Illuminate\Support\Facades\DB;

try {
    echo "Resetting warehouse inventory to zero...\n";
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    
    // Set the quantity to zero with a note
    $warehouseService->setWarehouseQuantity(0, 'Reset inventory to zero on ' . date('Y-m-d H:i:s'));
    
    // Verify the reset
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity: " . number_format($currentQuantity) . " blankets\n";
    
    // Calculate available pack quantities (should all be zero)
    echo "\nAvailable Pack Quantities:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
    foreach ($packSizes as $size) {
        $available = floor($currentQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    // Add a note about the upcoming shipment
    echo "\nAdding note about upcoming shipment of 2000 blankets...\n";
    
    // Update the notes field directly in the database
    $record = WarehouseQuantity::first();
    if ($record) {
        $record->notes = 'Expecting shipment of 2000 blankets next week (delivery date: ' . date('Y-m-d', strtotime('+1 week')) . ')';
        $record->save();
        
        echo "Note added successfully.\n";
    }
    
    echo "\nInventory has been reset to zero. The warehouse manager page should now show 0 blankets.\n";
    echo "A note has been added about the upcoming shipment of 2000 blankets next week.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error resetting warehouse quantity: " . $e->getMessage());
}
