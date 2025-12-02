<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contact Form Submissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- FIX: Scrollable Wrapper --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium uppercase whitespace-nowrap">Status</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase whitespace-nowrap">Name</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase whitespace-nowrap">Email</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase min-w-[150px]">Subject</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase whitespace-nowrap">Date</th>
                                    <th class="px-6 py-3 text-right font-medium uppercase whitespace-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($submissions as $submission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($submission->is_seen)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Seen</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">New</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($submission->subject, 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            @if (!$submission->is_seen)
                                                <form method="POST" action="{{ route('admin.contact.update', $submission) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-bold">Mark as Seen</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No contact submissions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $submissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>