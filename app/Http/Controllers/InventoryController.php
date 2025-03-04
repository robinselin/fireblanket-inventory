<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index()
    {
        // Check if the packs table exists
        if (!Schema::hasTable('packs')) {
            try {
                // Run migrations
                Artisan::call('migrate', ['--force' => true]);
                
                // Run seeders
                Artisan::call('db:seed', ['--force' => true]);
                
                // Redirect to inventory dashboard
                return redirect()->route('inventory.dashboard');
            } catch (\Exception $e) {
                return view('inventory.error', ['error' => $e->getMessage()]);
            }
        }
        
        // Check if packs table has data
        if (Pack::count() === 0) {
            try {
                // Run seeders
                Artisan::call('db:seed', ['--force' => true]);
            } catch (\Exception $e) {
                // Continue even if seeding fails
            }
        }
        
        // Redirect to inventory dashboard
        return redirect()->route('inventory.dashboard');
    }
}
