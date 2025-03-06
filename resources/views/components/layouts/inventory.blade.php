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
                    <div class="flex space-x-4">
                        <a href="{{ route('inventory.dashboard') }}" class="text-indigo-600 hover:text-indigo-900">Inventory</a>
                        <a href="{{ route('inventory.warehouse') }}" class="text-indigo-600 hover:text-indigo-900">Warehouse</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-900">Log in</a>
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-900">Register</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main>
                @if (session()->has('message'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    </div>
                @endif
                
                @if (session()->has('error'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                {{ $slot }}
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
