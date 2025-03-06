<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Jobs\UpdateShopifyInventoryJob;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Log;

try {
    echo "Starting Shopify inventory sync...\n";
    
    // Get the current warehouse quantity
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "Current warehouse quantity: " . $currentQuantity . "\n";
    
    // Dispatch the job synchronously (without queuing)
    echo "Dispatching Shopify sync job...\n";
    (new UpdateShopifyInventoryJob())->handle($warehouseService);
    
    echo "Shopify inventory sync completed successfully!\n";
    echo "Please check http://fb-inventory.test/inventory/warehouse to verify the updated quantities.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Manual Shopify sync failed: " . $e->getMessage());
}
