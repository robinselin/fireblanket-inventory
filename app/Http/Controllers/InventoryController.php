<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index()
    {
        try {
            // Check if the packs table exists
            if (!Schema::hasTable('packs')) {
                try {
                    // Run migrations
                    Log::info('Running migrations...');
                    Artisan::call('migrate', ['--force' => true]);
                    
                    // Run seeders
                    Log::info('Running seeders...');
                    Artisan::call('db:seed', ['--force' => true]);
                    
                    // Redirect to inventory dashboard
                    return redirect()->route('inventory.dashboard');
                } catch (Exception $e) {
                    Log::error('Migration or seeding error: ' . $e->getMessage());
                    return view('inventory.error', [
                        'error' => 'Error setting up database: ' . $e->getMessage()
                    ])->with('layout', 'components.layouts.inventory');
                }
            }
            
            // Check if packs table has data
            try {
                $packCount = DB::table('packs')->count();
                if ($packCount === 0) {
                    try {
                        // Run seeders
                        Log::info('Running seeders for empty packs table...');
                        Artisan::call('db:seed', ['--force' => true]);
                    } catch (Exception $e) {
                        Log::error('Seeding error: ' . $e->getMessage());
                        // Continue even if seeding fails
                    }
                }
            } catch (Exception $e) {
                Log::error('Error checking pack count: ' . $e->getMessage());
                // Continue even if check fails
            }
            
            // Redirect to inventory dashboard
            return redirect()->route('inventory.dashboard');
        } catch (Exception $e) {
            Log::error('General setup error: ' . $e->getMessage());
            return view('inventory.error', [
                'error' => 'General error setting up inventory: ' . $e->getMessage()
            ])->with('layout', 'components.layouts.inventory');
        }
    }
    
    public function fallback()
    {
        return view('inventory.fallback')->with('layout', 'components.layouts.inventory');
    }
}
