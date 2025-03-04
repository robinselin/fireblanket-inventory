<?php

namespace App\Livewire\Inventory;

use App\Models\Order;
use App\Models\Pack;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $months;
    public $years;
    
    public $showOrderModal = false;
    public $showDeleteModal = false;
    public $editingOrder = false;
    public $selectedOrderId = null;
    public $selectedPackId = null;
    
    public $orderForm = [
        'quantity' => 1,
        'order_date' => '',
        'notes' => '',
    ];
    
    protected $rules = [
        'orderForm.quantity' => 'required|integer|min:1',
        'orderForm.order_date' => 'required|date',
        'orderForm.notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        
        $currentYear = Carbon::now()->year;
        $this->years = range($currentYear - 2, $currentYear + 2);
        
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = $currentYear;
        $this->orderForm['order_date'] = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $packs = Pack::with(['orders' => function ($query) {
            $query->whereMonth('order_date', $this->selectedMonth)
                  ->whereYear('order_date', $this->selectedYear);
        }])->orderBy('size')->get();
        
        // Add a property to each pack with the current month's orders
        $packs->each(function ($pack) {
            $pack->currentMonthOrders = $pack->orders;
        });
        
        return view('livewire.inventory.dashboard', [
            'packs' => $packs,
        ]);
    }
    
    public function addOrder($packId)
    {
        $this->resetOrderForm();
        $this->selectedPackId = $packId;
        $this->editingOrder = false;
        $this->showOrderModal = true;
    }
    
    public function editOrder($orderId)
    {
        $this->editingOrder = true;
        $this->selectedOrderId = $orderId;
        
        $order = Order::findOrFail($orderId);
        $this->selectedPackId = $order->pack_id;
        
        $this->orderForm = [
            'quantity' => $order->quantity,
            'order_date' => $order->order_date->format('Y-m-d'),
            'notes' => $order->notes,
        ];
        
        $this->showOrderModal = true;
    }
    
    public function storeOrder()
    {
        $this->validate();
        
        Order::create([
            'pack_id' => $this->selectedPackId,
            'quantity' => $this->orderForm['quantity'],
            'order_date' => $this->orderForm['order_date'],
            'notes' => $this->orderForm['notes'],
        ]);
        
        $this->closeOrderModal();
        session()->flash('message', 'Order added successfully.');
    }
    
    public function updateOrder()
    {
        $this->validate();
        
        $order = Order::findOrFail($this->selectedOrderId);
        $order->update([
            'quantity' => $this->orderForm['quantity'],
            'order_date' => $this->orderForm['order_date'],
            'notes' => $this->orderForm['notes'],
        ]);
        
        $this->closeOrderModal();
        session()->flash('message', 'Order updated successfully.');
    }
    
    public function deleteOrder($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->showDeleteModal = true;
    }
    
    public function confirmDelete()
    {
        $order = Order::findOrFail($this->selectedOrderId);
        $order->delete();
        
        $this->closeDeleteModal();
        session()->flash('message', 'Order deleted successfully.');
    }
    
    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->resetOrderForm();
    }
    
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedOrderId = null;
    }
    
    private function resetOrderForm()
    {
        $this->orderForm = [
            'quantity' => 1,
            'order_date' => Carbon::now()->format('Y-m-d'),
            'notes' => '',
        ];
        $this->selectedOrderId = null;
    }
}
