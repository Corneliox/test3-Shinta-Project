<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Name</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Role Selection --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">Initial Role</label>
                        <select name="role" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="user">Regular User</option>
                            <option value="artist">Artist</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create User
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>