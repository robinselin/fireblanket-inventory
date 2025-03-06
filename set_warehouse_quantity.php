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
    
    // Add 2000 blankets to the current quantity
    $newQuantity = $currentQuantity + 2000;
    echo "Setting new warehouse quantity to: " . number_format($newQuantity) . " blankets\n";
    
    // Set the new quantity
    $warehouseService->setWarehouseQuantity($newQuantity, 'Added 2000 blankets via script on ' . date('Y-m-d H:i:s'));
    
    // Get the updated quantity to verify
    $updatedQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Updated warehouse quantity: " . number_format($updatedQuantity) . " blankets\n";
    
    // Calculate available pack quantities
    echo "\nUpdated Available Pack Quantities:\n";
    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
    foreach ($packSizes as $size) {
        $available = floor($updatedQuantity / $size);
        echo $size . "-pack: " . number_format($available) . "\n";
    }
    
    echo "\nThe database has been updated. Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    
    // Verify the database record directly
    $record = WarehouseQuantity::first();
    echo "\nDirect database record check:\n";
    echo "ID: " . $record->id . "\n";
    echo "Total Quantity: " . number_format($record->total_quantity) . "\n";
    echo "Notes: " . ($record->notes ?? 'None') . "\n";
    echo "Updated at: " . $record->updated_at . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error setting warehouse quantity: " . $e->getMessage());
}
