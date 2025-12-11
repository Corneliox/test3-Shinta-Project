@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-4">Manage All Artworks</h2>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="p-3 border-b">Image</th>
                        <th class="p-3 border-b">Title</th>
                        <th class="p-3 border-b">Artist</th>
                        <th class="p-3 border-b">Stock</th>
                        <th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($artworks as $art)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border-b">
                            <img src="{{ Storage::url($art->image_path) }}" class="w-12 h-12 object-cover rounded">
                        </td>
                        <td class="p-3 border-b">{{ $art->title }}</td>
                        <td class="p-3 border-b">{{ $art->user->name }}</td>
                        <td class="p-3 border-b">
                            {{ $art->stock }}
                            @if($art->stock == 0) <span class="text-red-500 text-xs font-bold">(OOS)</span> @endif
                        </td>
                        <td class="p-3 border-b">
                            {{-- Reuse the standard edit route --}}
                            <a href="{{ route('artworks.edit', $art->id) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            
                            <form action="{{ route('artworks.destroy', $art->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete permanently?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">
                {{ $artworks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection