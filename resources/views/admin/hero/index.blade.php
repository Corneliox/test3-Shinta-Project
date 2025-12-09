<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Hero Carousel') }}
            </h2>
            <a href="{{ route('admin.hero.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Add New Image
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($images as $img)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg relative group">
                        <img src="{{ Storage::url($img->image_path) }}" class="w-full h-48 object-cover">
                        
                        <div class="p-4 flex justify-between items-center">
                            <span class="text-xs text-gray-500">Uploaded: {{ $img->created_at->diffForHumans() }}</span>
                            
                            <form action="{{ route('admin.hero.destroy', $img) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-gray-500 py-10">
                        No images in carousel. Add one to get started!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>