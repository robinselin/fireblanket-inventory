<?php

use App\Http\Controllers\InventoryController;
use App\Livewire\Inventory\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Inventory routes
Route::get('/inventory/setup', [InventoryController::class, 'index'])->name('inventory.setup');
Route::get('/inventory/fallback', [InventoryController::class, 'fallback'])->name('inventory.fallback');
Route::get('/inventory', Dashboard::class)->name('inventory.dashboard');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
