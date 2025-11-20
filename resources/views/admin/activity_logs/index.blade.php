<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Loop through the Months (Groups) --}}
            @foreach ($groupedLogs as $month => $logs)
                
                {{-- Month Header --}}
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mt-8 mb-4 px-2 border-b dark:border-gray-700">
                    {{ $month }}
                </h3>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">Admin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/2">Description</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-1/6">Time</th>
                                </td>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Loop through logs in this month --}}
                                @foreach ($logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-sm">
                                        {{ $log->user->name ?? 'System/Deleted' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ str_contains($log->action, 'Delete') ? 'bg-red-100 text-red-800' : (str_contains($log->action, 'Create') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $log->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ $log->created_at->format('d M, H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            {{-- Show message if no logs --}}
            @if($groupedLogs->isEmpty())
                <div class="text-center text-gray-500 py-10">
                    No activity logs found.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>