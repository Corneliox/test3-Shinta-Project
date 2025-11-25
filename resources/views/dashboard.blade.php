<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome back, Admin! Here is your overview.") }}
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. PENDING ORDERS SECTION (HIGH PRIORITY)                --}}
    {{-- ======================================================== --}}
    @if(isset($pendingOrders) && $pendingOrders->count() > 0)
        <div class="pb-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3 class="mb-4 text-lg font-bold text-red-600 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pending Orders (Action Required)
                </h3>
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artwork</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artist</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timer (6H)</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($pendingOrders as $item)
                                    <tr>
                                        {{-- Item Name & Image --}}
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($item->image_path) }}" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $item->title }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Artist Name --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->user->name }}
                                        </td>

                                        {{-- Price --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </td>

                                        {{-- Countdown Timer --}}
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($item->reserved_until && now()->lessThan($item->reserved_until))
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Expires in {{ now()->diffInHours($item->reserved_until) }}h {{ now()->diffInMinutes($item->reserved_until) % 60 }}m
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Expired
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Actions Buttons --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                {{-- CONFIRM (Deal Done) --}}
                                                <form action="{{ route('admin.orders.confirm', $item) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs transition" onclick="return confirm('Mark this item as SOLD? (Money Received)')">
                                                        Confirm
                                                    </button>
                                                </form>

                                                {{-- CANCEL (Deal Failed) --}}
                                                <form action="{{ route('admin.orders.reject', $item) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs transition" onclick="return confirm('Cancel reservation and return item to stock?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- 2. CONTACT FEEDBACK SECTION                              --}}
    {{-- ======================================================== --}}
    @if(isset($unseenSubmissions) && $unseenSubmissions->count() > 0)
        <div class="pb-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">New Feedback</h3>
                
                @foreach($unseenSubmissions as $submission)
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms 
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4 relative">
                        
                        <div class="p-6 text-gray-900 dark:text-gray-100 pr-12">
                            <div class="font-bold text-lg mb-1">{{ $submission->subject }}</div>
                            <p class="text-gray-600 dark:text-gray-300 mb-3">"{{ $submission->feedback }}"</p>
                            <div class="text-sm text-gray-500">
                                From: {{ $submission->name }} ({{ $submission->email }}) 
                                <span class="mx-2">•</span> 
                                {{ $submission->created_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Green Tick Button --}}
                        <form method="POST" action="{{ route('admin.contact.update', $submission) }}" 
                              class="absolute top-4 right-4"
                              @submit.prevent="show = false; setTimeout(() => $el.submit(), 300)">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-gray-400 hover:text-green-500 transition-colors" title="Mark as Seen">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-app-layout>