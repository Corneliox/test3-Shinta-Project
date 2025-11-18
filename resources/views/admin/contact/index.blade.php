<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contact Form Submissions') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Action</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{-- You could add a "View" button here to see the full feedback --}}
                                @if (!$submission->is_seen)
                                    <form method="POST" action="{{ route('admin.contact.update', $submission) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">Mark as Seen</button>
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
            <div class="mt-4">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>