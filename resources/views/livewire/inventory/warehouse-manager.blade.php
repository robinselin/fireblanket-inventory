<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Warehouse Inventory Manager</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Current Warehouse Quantity</h3>
        <p class="text-3xl font-bold">{{ number_format($currentQuantity) }} blankets</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Set Master Quantity -->
        <div class="space-y-4 border p-4 rounded-lg bg-blue-50">
            <h3 class="text-lg font-semibold">Set Master Quantity</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Blankets</label>
                <input type="number" wire:model="masterQuantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                @error('masterQuantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea wire:model="masterNotes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Reason for setting master quantity"></textarea>
            </div>

            <button wire:click="setMasterQuantity" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Set Master Quantity
            </button>
        </div>
        
        <!-- Adjust Quantity -->
        <div class="space-y-4 border p-4 rounded-lg">
            <h3 class="text-lg font-semibold">Adjust Quantity</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                <select wire:model="adjustmentType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="add">Add Stock</option>
                    <option value="remove">Remove Stock</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" wire:model="adjustmentQuantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1">
                @error('adjustmentQuantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <button wire:click="adjustQuantity" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Warehouse Quantity
            </button>
        </div>

        <!-- Available Pack Quantities -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Available Pack Quantities</h3>
            <div class="space-y-2 border p-4 rounded-lg">
                @php
                    $packSizes = [1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100];
                @endphp
                
                @foreach($packSizes as $size)
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-medium">{{ $size }}-pack:</span>
                        <span class="text-lg">{{ floor($currentQuantity / $size) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
