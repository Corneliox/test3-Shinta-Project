<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        
        {{-- 1. The root div is now a vertical flex container that fills the screen --}}
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
            
            {{-- 2. The navigation is the first item --}}
            @include('layouts.navigation')

            {{-- 3. This horizontal flex container will grow to fill the *rest* of the screen --}}
            <div class="flex flex-1">

                {{-- 4. The sidebar is now a vertical flex container itself --}}
                <aside class="w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 hidden md:block flex flex-col">
                    
                    {{-- This <nav> block will be pushed to the bottom --}}
                    <nav class="p-4 space-y-2 mt-auto">
                        <a href="{{ route('dashboard') }}"
                           class="block px-3 py-2 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                            Dashboard
                        </a>
                    
                        <a href="{{ route('admin.users.index') }}"
                           class="block px-3 py-2 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                            User Management
                        </a>
                    
                        <a href="{{ route('admin.events.index') }}"
                           class="block px-3 py-2 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.events.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                            Events Manager
                        </a>

                        {{-- ADD THIS NEW LINK --}}
                        <a href="{{ route('admin.contact.index') }}"
                        class="block px-3 py-2 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.contact.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                            Contact Form
                        </a>
                    </nav>
                </aside>

                {{-- 5. The main content area will fill the remaining horizontal space --}}
                <main class="flex-1 overflow-y-auto"> {{-- Added overflow-y-auto for scrolling --}}
                    @if (isset($header))
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>