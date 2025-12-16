<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('News & Articles') }}
            </h2>
            <a href="{{ route('admin.news.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                Write New Article
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Thumbnail</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Title</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($news as $article)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($article->thumbnail)
                                            <img src="{{ Storage::url($article->thumbnail) }}" class="h-10 w-16 object-cover rounded shadow-sm">
                                        @else
                                            <span class="text-gray-400 text-xs">No Img</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">
                                        {{ $article->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($article->is_published)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ $article->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.news.edit', $article) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Edit</a>
                                            
                                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('Delete this article permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No news articles found. <a href="{{ route('admin.news.create') }}" class="text-blue-600 underline">Create one now</a>.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $news->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>