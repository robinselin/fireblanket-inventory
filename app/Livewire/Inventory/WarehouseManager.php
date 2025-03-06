<?php

namespace App\Livewire\Inventory;

use App\Services\WarehouseService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class WarehouseManager extends Component
{
    public $currentQuantity = 0;
    public $adjustmentQuantity = 0;
    public $adjustmentType = 'add';
    public $notes = '';
    
    // New properties for setting master quantity
    public $masterQuantity = 0;
    public $masterNotes = '';
    
    protected $rules = [
        'adjustmentQuantity' => 'required|integer|min:1',
        'adjustmentType' => 'required|in:add,remove',
        'notes' => 'nullable|string',
        'masterQuantity' => 'required|integer|min:0',
        'masterNotes' => 'nullable|string'
    ];

    public function mount(WarehouseService $warehouseService)
    {
        $this->currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
        $this->masterQuantity = $this->currentQuantity;
    }

    public function render()
    {
        return view('livewire.inventory.warehouse-manager');
    }

    public function adjustQuantity(WarehouseService $warehouseService)
    {
        $this->validate([
            'adjustmentQuantity' => 'required|integer|min:1',
            'adjustmentType' => 'required|in:add,remove',
            'notes' => 'nullable|string'
        ]);

        try {
            $type = $this->adjustmentType === 'add' ? 'restock' : 'sale';
            $quantity = $this->adjustmentQuantity;

            $warehouseService->adjustWarehouseQuantity($quantity, 1, $type);
            
            $this->currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
            $this->masterQuantity = $this->currentQuantity;
            $this->reset(['adjustmentQuantity', 'notes']);
            
            session()->flash('message', 'Warehouse quantity updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error adjusting warehouse quantity: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
        }
    }
    
    // New method for setting the master quantity
    public function setMasterQuantity(WarehouseService $warehouseService)
    {
        $this->validate([
            'masterQuantity' => 'required|integer|min:0',
            'masterNotes' => 'nullable|string'
        ]);
        
        try {
            $warehouseService->setWarehouseQuantity($this->masterQuantity, $this->masterNotes);
            
            $this->currentQuantity = $warehouseService->getCurrentWarehouseQuantity();
            $this->reset(['masterNotes']);
            
            session()->flash('message', 'Master warehouse quantity set successfully.');
        } catch (\Exception $e) {
            Log::error('Error setting master warehouse quantity: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
        }
    }
}
