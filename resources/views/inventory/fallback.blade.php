<div>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Fire Blanket Inventory
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    The inventory system is currently being set up.
                </p>
            </div>
            <div class="border-t border-gray-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex flex-col items-center justify-center py-8">
                        <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-lg text-gray-700">
                            Please wait while we set up the database...
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            This may take a few moments.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('inventory.setup') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Run Setup
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
