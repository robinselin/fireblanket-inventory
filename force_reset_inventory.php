<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    echo "Checking current warehouse_quantity records...\n";
    
    // Get all records from the warehouse_quantity table
    $records = DB::table('warehouse_quantity')->get();
    
    echo "Found " . count($records) . " records in warehouse_quantity table.\n";
    
    // Display all records
    foreach ($records as $record) {
        echo "ID: " . $record->id . ", Quantity: " . number_format($record->total_quantity) . ", Notes: " . ($record->notes ?? 'None') . "\n";
    }
    
    echo "\nForcing reset of all warehouse quantities to zero...\n";
    
    // Update all records to zero
    $updated = DB::table('warehouse_quantity')->update([
        'total_quantity' => 0,
        'notes' => 'Expecting shipment of 2000 blankets next week (delivery date: ' . date('Y-m-d', strtotime('+1 week')) . ')',
        'updated_at' => now()
    ]);
    
    echo "Updated " . $updated . " records.\n";
    
    // Verify the reset
    $records = DB::table('warehouse_quantity')->get();
    
    echo "\nVerifying reset:\n";
    foreach ($records as $record) {
        echo "ID: " . $record->id . ", Quantity: " . number_format($record->total_quantity) . ", Notes: " . ($record->notes ?? 'None') . "\n";
    }
    
    echo "\nInventory has been reset to zero. The warehouse manager page should now show 0 blankets.\n";
    echo "A note has been added about the upcoming shipment of 2000 blankets next week.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error forcibly resetting warehouse quantity: " . $e->getMessage());
}
