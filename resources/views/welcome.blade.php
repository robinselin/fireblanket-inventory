<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fire Blanket Inventory</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100 dark:bg-zinc-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        Fire Blanket Inventory Tracker
                    </h1>
                    <p class="mt-3 text-xl text-gray-500 dark:text-gray-400">
                        Track and manage your Fire Blanket inventory
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('inventory.setup') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Go to Inventory
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
