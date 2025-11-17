{{-- This is the sidebar navigation menu --}}
<nav class="p-4 space-y-2">
    
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
</nav>