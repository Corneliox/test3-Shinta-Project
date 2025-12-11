@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Edit User: {{ $user->name }}</h2>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')

                {{-- Name --}}
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded border-gray-300">
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded border-gray-300">
                </div>

                {{-- Password (Optional) --}}
                <div class="mb-4 p-4 border rounded bg-gray-50 dark:bg-gray-700">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold">Change Password (Optional)</label>
                    <small class="text-gray-500 mb-2 block">Leave blank to keep the current password.</small>
                    
                    <input type="password" name="password" placeholder="New Password" class="w-full rounded border-gray-300 mb-2">
                    <input type="password" name="password_confirmation" placeholder="Confirm New Password" class="w-full rounded border-gray-300">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection