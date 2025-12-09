<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload Hero Image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.hero.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Select Image</label>
                        <input type="file" name="image" class="w-full text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required>
                        <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x600px (Landscape). Max 5MB.</p>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.hero.index') }}" class="text-gray-500 underline mr-4 mt-2">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Upload
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>