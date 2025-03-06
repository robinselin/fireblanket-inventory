<?php

namespace App\Services;

use App\Models\WarehouseQuantity;
use Illuminate\Support\Facades\Log;
use App\Jobs\UpdateShopifyInventoryJob;

class WarehouseService
{
    public function getCurrentWarehouseQuantity(): int
    {
        // Get the quantity from the database, or create a record if it doesn't exist
        $warehouse = WarehouseQuantity::firstOrCreate(
            [],
            ['total_quantity' => 1972628] // Initialize with our current quantity if no record exists
        );
        
        return $warehouse->total_quantity;
    }

    public function calculateAvailablePackQuantity(int $packSize): int
    {
        $totalBlankets = $this->getCurrentWarehouseQuantity();
        return floor($totalBlankets / $packSize);
    }

    public function adjustWarehouseQuantity(int $quantity, int $packSize, string $type = 'sale'): bool
    {
        try {
            $adjustment = $quantity * $packSize;
            
            $warehouse = WarehouseQuantity::firstOrCreate(
                [],
                ['total_quantity' => 0]
            );

            if ($type === 'sale') {
                // Check if we have enough inventory
                if ($warehouse->total_quantity < $adjustment) {
                    throw new \Exception('Insufficient warehouse quantity');
                }
                $warehouse->total_quantity -= $adjustment;
            } else {
                $warehouse->total_quantity += $adjustment;
            }

            $warehouse->save();

            // Dispatch Shopify sync job
            dispatch(new UpdateShopifyInventoryJob());

            return true;
        } catch (\Exception $e) {
            Log::error('Error adjusting warehouse quantity: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // New method to set the master warehouse quantity directly
    public function setWarehouseQuantity(int $quantity, string $notes = ''): bool
    {
        try {
            $warehouse = WarehouseQuantity::firstOrCreate(
                [],
                ['total_quantity' => 0]
            );
            
            $warehouse->total_quantity = $quantity;
            $warehouse->notes = $notes;
            $warehouse->save();
            
            // Dispatch Shopify sync job
            dispatch(new UpdateShopifyInventoryJob());
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error setting warehouse quantity: ' . $e->getMessage());
            throw $e;
        }
    }
}
