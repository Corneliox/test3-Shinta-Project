<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'WOPANCO Admin') }}</title>
        
        {{-- FAVICON --}}
        <link rel="icon" type="image/png" href="{{ asset('images/wopanco2.png') }}">
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900" x-data="{ sidebarOpen: false }">
        
        {{-- ================================================= --}}
        {{-- 1. FIXED TOP NAVIGATION BAR                       --}}
        {{-- ================================================= --}}
        {{-- ================= HEADER (Fixed Top) ================= --}}
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 h-16 flex items-center px-4 justify-between fixed top-0 w-full z-50">
                
                {{-- Left: Hamburger & Logo --}}
                <div class="flex items-center">
                    {{-- Mobile Hamburger --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Logo --}}
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/wopanco2.png') }}" class="h-8 w-auto mr-2" />
                        <span class="font-bold text-xl text-gray-800 dark:text-gray-200 hidden md:block">Admin Panel</span>
                    </a>
                </div>

                {{-- Right: User Dropdown --}}
                <div class="flex items-center relative" x-data="{ userMenuOpen: false }">
                    
                    {{-- 1. The Trigger Button (Name + Icon) --}}
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                        <div>{{ Auth::user()->name }}</div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    {{-- 2. The Dropdown Menu --}}
                    <div x-show="userMenuOpen" 
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 top-10 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        
                        {{-- PROFILE LINK --}}
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            {{ __('Profile') }}
                        </a>

                        <div class="border-t border-gray-100 dark:border-gray-600"></div>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>

                </div>
            </nav>

        {{-- ================================================= --}}
        {{-- 2. FIXED SIDEBAR NAVIGATION                       --}}
        {{-- ================================================= --}}
        <aside id="logo-sidebar" 
               class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               aria-label="Sidebar">
            
            {{-- Include the cleaned-up Sidebar Component --}}
            @include('layouts.admin-sidebar')
        </aside>

        {{-- ================================================= --}}
        {{-- 3. MOBILE OVERLAY (Click to close sidebar)        --}}
        {{-- ================================================= --}}
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-50"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-50"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 sm:hidden"
             style="display: none;"> {{-- Added style="display: none;" to prevent flash on load --}}
        </div>

        {{-- ================================================= --}}
        {{-- 4. MAIN CONTENT AREA                              --}}
        {{-- ================================================= --}}
        {{-- 'mt-14' pushes content down below the fixed header --}}
        {{-- 'sm:ml-64' pushes content right, next to the fixed sidebar on desktop --}}
        <div class="p-4 sm:ml-64 mt-14">
            
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow mb-4 rounded-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

    </body>
</html>