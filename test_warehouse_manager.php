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
use App\Livewire\Inventory\WarehouseManager;

try {
    echo "Testing Warehouse Manager Component...\n\n";
    
    // Check database records
    $records = DB::table('warehouse_quantity')->get();
    echo "Database Records:\n";
    foreach ($records as $record) {
        echo "ID: " . $record->id . ", Quantity: " . number_format($record->total_quantity) . ", Notes: " . ($record->notes ?? 'None') . "\n";
    }
    
    // Get the warehouse service
    $warehouseService = app(WarehouseService::class);
    $currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
    echo "\nWarehouseService::getCurrentWarehouseQuantity() returns: " . number_format($currentQuantity) . "\n";
    
    // Test the Livewire component
    $component = new WarehouseManager();
    $component->mount($warehouseService);
    echo "WarehouseManager component currentQuantity: " . number_format($component->currentQuantity) . "\n";
    
    // Check if the warehouse.blade.php view exists
    $warehouseViewPath = resource_path('views/inventory/warehouse.blade.php');
    echo "\nWarehouse view exists: " . (file_exists($warehouseViewPath) ? 'Yes' : 'No') . "\n";
    
    // Check if the warehouse-manager.blade.php view exists
    $warehouseManagerViewPath = resource_path('views/livewire/inventory/warehouse-manager.blade.php');
    echo "Warehouse manager view exists: " . (file_exists($warehouseManagerViewPath) ? 'Yes' : 'No') . "\n";
    
    // Check the route
    $routes = app('router')->getRoutes();
    $warehouseRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'inventory/warehouse') {
            $warehouseRoute = $route;
            break;
        }
    }
    echo "Warehouse route exists: " . ($warehouseRoute ? 'Yes' : 'No') . "\n";
    if ($warehouseRoute) {
        echo "Route action: " . json_encode($warehouseRoute->getAction()) . "\n";
    }
    
    echo "\nTroubleshooting Steps:\n";
    echo "1. Ensure the warehouse route is properly defined in routes/web.php\n";
    echo "2. Verify that the WarehouseManager Livewire component is registered\n";
    echo "3. Check that Livewire is properly installed and configured\n";
    echo "4. Try clearing Laravel's cache with: php artisan cache:clear\n";
    echo "5. Try clearing Livewire's cache with: php artisan livewire:discover\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Error testing warehouse manager: " . $e->getMessage());
}
