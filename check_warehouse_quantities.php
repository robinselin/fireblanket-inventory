<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import the necessary classes
use App\Services\WarehouseService;
use App\Models\WarehouseQuantity;

// Get the current warehouse quantity
$warehouseService = app(WarehouseService::class);
$currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
echo "Current warehouse quantity: " . number_format($currentQuantity) . " blankets\n\n";

// Get the raw database record
$record = WarehouseQuantity::first();
echo "Database record details:\n";
echo "ID: " . $record->id . "\n";
echo "Total Quantity: " . number_format($record->total_quantity) . "\n";
echo "Notes: " . ($record->notes ?? 'None') . "\n";
echo "Created at: " . $record->created_at . "\n";
echo "Updated at: " . $record->updated_at . "\n\n";

// Calculate available pack quantities
echo "Available Pack Quantities:\n";
$packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
foreach ($packSizes as $size) {
    $available = floor($currentQuantity / $size);
    echo $size . "-pack: " . number_format($available) . "\n";
}

echo "\nNote: While the local database is updated correctly, the Shopify sync is failing due to API permission issues.\n";
echo "The API token needs the 'write_inventory' scope to update Shopify inventory quantities.\n";
