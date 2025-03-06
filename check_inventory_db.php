<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Models\WarehouseQuantity;
use Illuminate\Support\Facades\DB;

try {
    echo "Checking warehouse inventory database records...\n\n";
    
    // Get all records from warehouse_quantity table
    $records = DB::table('warehouse_quantity')->get();
    
    echo "Found " . count($records) . " records in warehouse_quantity table:\n";
    foreach ($records as $record) {
        echo "ID: " . $record->id . 
             ", Quantity: " . number_format($record->total_quantity) . 
             ", Created: " . $record->created_at . 
             ", Updated: " . $record->updated_at . 
             ", Notes: " . ($record->notes ?? 'None') . "\n";
    }
    
    // Check for any recent order records
    echo "\nChecking for recent order records:\n";
    if (DB::getSchemaBuilder()->hasTable('orders')) {
        $recentOrders = DB::table('orders')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        if (count($recentOrders) > 0) {
            foreach ($recentOrders as $order) {
                echo "Order ID: " . $order->id . 
                     ", Quantity: " . $order->quantity . 
                     ", Date: " . $order->created_at . 
                     ", Notes: " . ($order->notes ?? 'None') . "\n";
            }
        } else {
            echo "No recent orders found.\n";
        }
    } else {
        echo "Orders table does not exist.\n";
    }
    
    // Check for any recent inventory adjustments
    echo "\nChecking for recent inventory adjustments:\n";
    if (DB::getSchemaBuilder()->hasTable('inventory_adjustments')) {
        $recentAdjustments = DB::table('inventory_adjustments')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        if (count($recentAdjustments) > 0) {
            foreach ($recentAdjustments as $adjustment) {
                echo "Adjustment ID: " . $adjustment->id . 
                     ", Quantity: " . $adjustment->quantity . 
                     ", Type: " . $adjustment->type . 
                     ", Date: " . $adjustment->created_at . 
                     ", Notes: " . ($adjustment->notes ?? 'None') . "\n";
            }
        } else {
            echo "No recent inventory adjustments found.\n";
        }
    } else {
        echo "Inventory adjustments table does not exist.\n";
    }
    
    // Check the current warehouse quantity using the service
    echo "\nCurrent warehouse quantity from WarehouseService:\n";
    $warehouseService = app(\App\Services\WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current quantity: " . number_format($currentQuantity) . " blankets\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
