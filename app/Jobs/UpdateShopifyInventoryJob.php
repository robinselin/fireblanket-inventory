<?php

namespace App\Jobs;

use App\Services\WarehouseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateShopifyInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function handle(WarehouseService $warehouseService)
    {
        try {
            // Get all products from Shopify
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => config('shopify.access_token'),
            ])->get("https://" . config('shopify.shop_domain') . "/admin/api/" . config('shopify.api_version') . "/products.json");

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch Shopify products: ' . $response->body());
            }

            $products = $response->json()['products'];

            // Update each variant's inventory
            foreach ($products as $product) {
                foreach ($product['variants'] as $variant) {
                    if (preg_match('/(\d+)[-\s]?pack/i', $variant['title'], $matches)) {
                        $packSize = (int) $matches[1];
                        $availableQuantity = $warehouseService->calculateAvailablePackQuantity($packSize);

                        // Update Shopify inventory
                        $inventoryItemId = $variant['inventory_item_id'];
                        $response = Http::withHeaders([
                            'X-Shopify-Access-Token' => config('shopify.access_token'),
                        ])->post("https://" . config('shopify.shop_domain') . "/admin/api/" . config('shopify.api_version') . "/inventory_levels/set.json", [
                            'inventory_item_id' => $inventoryItemId,
                            'location_id' => config('shopify.location_id'),
                            'available' => $availableQuantity
                        ]);

                        if (!$response->successful()) {
                            Log::error("Failed to update Shopify inventory for variant {$variant['id']}: " . $response->body());
                            continue;
                        }

                        Log::info("Updated Shopify inventory for {$packSize}-pack: {$availableQuantity}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Shopify sync failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
