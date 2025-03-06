<?php

namespace App\Console\Commands;

use App\Models\WarehouseQuantity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncShopifyInventory extends Command
{
    protected $signature = 'shopify:sync-inventory';
    protected $description = 'Sync inventory from Shopify to local warehouse';

    public function handle()
    {
        try {
            $this->info('Starting Shopify inventory sync...');

            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => config('shopify.access_token'),
            ])->get("https://" . config('shopify.shop_domain') . "/admin/api/" . config('shopify.api_version') . "/products.json");

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch Shopify products: ' . $response->body());
            }

            $products = $response->json()['products'];
            $totalBlankets = 0;

            foreach ($products as $product) {
                foreach ($product['variants'] as $variant) {
                    // Extract pack size from variant title or SKU
                    if (preg_match('/(\d+)[-\s]?pack/i', $variant['title'], $matches)) {
                        $packSize = (int) $matches[1];
                        $variantQuantity = (int) $variant['inventory_quantity'];
                        
                        // Convert pack quantity to individual blankets
                        $totalBlankets += ($packSize * $variantQuantity);
                    }
                }
            }

            // Update warehouse quantity
            WarehouseQuantity::updateOrCreate(
                [],
                ['total_quantity' => $totalBlankets]
            );

            $this->info("Sync complete! Total warehouse quantity: {$totalBlankets} blankets");
            Log::info("Shopify inventory sync completed. Total warehouse quantity: {$totalBlankets}");

        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Shopify sync failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
