<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- "You're logged in!" message --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            {{ __("You're logged in!") }}
        </div>
    </div>

    {{-- NEW: Unseen Submissions --}}
    @if($unseenSubmissions->count() > 0)
        <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6 mb-2">
            New Feedback ({{ $unseenSubmissions->count() }})
        </h3>
    @endif

    @foreach($unseenSubmissions as $submission)
        {{-- This x-data is for Alpine.js to handle the fade out --}}
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm mt-4 relative">
                {{-- Green Tick Button --}}
                <form method="POST" action="{{ route('admin.contact.update', $submission) }}" @submit.prevent="show = false; $el.submit()">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="absolute top-4 right-4 text-green-500 hover:text-green-700" title="Mark as seen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                </form>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center mb-2">
                        <h4 class="font-semibold text-lg">{{ $submission->subject }}</h4>
                    </div>
                    <p class="mb-4 dark:text-gray-300">{{ $submission->feedback }}</p>
                    <hr class="dark:border-gray-700">
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        From: **{{ $submission->name }}** ({{ $submission->email }})
                        <span class="mx-2">|</span>
                        Received: {{ $submission->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</x-app-layout>