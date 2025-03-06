<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use App\Models\Pack;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    echo "Creating order entry and adjusting inventory for 2-pack order...\n\n";
    
    // First, get the pack ID for the 2-pack
    $pack = Pack::where('size', 2)->first();
    
    if (!$pack) {
        // Create the pack if it doesn't exist
        echo "2-pack not found in database, creating it...\n";
        $pack = new Pack();
        $pack->size = 2;
        $pack->name = '2-Pack';
        $pack->description = 'Fire Blanket 2-Pack';
        $pack->save();
        echo "Created 2-pack with ID: " . $pack->id . "\n";
    } else {
        echo "Found 2-pack with ID: " . $pack->id . "\n";
    }
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    
    // Get current quantity before adjustment
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity before order: " . number_format($currentQuantity) . " blankets\n";
    
    // Create a new order
    $order = new Order();
    $order->pack_id = $pack->id;
    $order->quantity = 1; // 1 unit of 2-pack
    $order->order_date = now();
    $order->notes = "2-pack order processed on " . now()->format('Y-m-d H:i:s');
    $order->save();
    
    echo "Created order entry with ID: " . $order->id . "\n";
    
    // Process the order - adjust warehouse quantity
    // For a 2-pack, we need to reduce by 2 blankets
    $packSize = 2;
    $quantity = 1; // 1 unit of 2-pack
    
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
    
    echo "\nOrder has been processed successfully and recorded in the database.\n";
    echo "The Shopify inventory sync job has been automatically dispatched.\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify the updated quantities.\n";
    
    // List recent orders
    echo "\nRecent orders in the database:\n";
    $recentOrders = Order::with('pack')->orderBy('created_at', 'desc')->limit(5)->get();
    
    if ($recentOrders->count() > 0) {
        foreach ($recentOrders as $recentOrder) {
            echo "Order ID: " . $recentOrder->id . 
                 ", Pack: " . $recentOrder->pack->size . "-pack" .
                 ", Quantity: " . $recentOrder->quantity . 
                 ", Date: " . $recentOrder->created_at . 
                 ", Notes: " . ($recentOrder->notes ?? 'None') . "\n";
        }
    } else {
        echo "No recent orders found.\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error creating order and adjusting inventory: " . $e->getMessage());
}
