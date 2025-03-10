<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="min-h-screen bg-gray-100">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2">
                            <x-app-logo />
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900">Fire Blanket Inventory</h1>
                    </div>
                </div>
            </header>

            <main>
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-red-50">
                            <h3 class="text-lg leading-6 font-medium text-red-800">
                                Error Setting Up Inventory
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-red-700">
                                We encountered an issue while setting up the inventory system.
                            </p>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="bg-red-50 p-4 rounded-md mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">
                                                Error Details
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>{{ $error ?? 'An unknown error occurred.' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <h4 class="text-md font-medium text-gray-900">Possible Solutions:</h4>
                                    <ul class="mt-2 list-disc pl-5 space-y-1 text-sm text-gray-700">
                                        <li>Check your database connection settings in the .env file</li>
                                        <li>Ensure your database server is running</li>
                                        <li>Verify that your database user has proper permissions</li>
                                        <li>Check Laravel logs for more detailed error information</li>
                                    </ul>
                                </div>
                                
                                <div class="mt-6 flex space-x-4">
                                    <a href="{{ route('inventory.setup') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Try Again
                                    </a>
                                    <a href="{{ route('inventory.fallback') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Go to Fallback Page
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="bg-white shadow mt-8">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-gray-500 text-sm">
                        &copy; {{ date('Y') }} Fire Blanket Inventory. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>

        @fluxScripts
    </body>
</html>
