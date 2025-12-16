<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            {{-- ADDED 'rich-editor' class here --}}
                            <textarea id="description" name="description" class="rich-editor block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- MAIN IMAGE --}}
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('Main Event Image')" />
                            <input id="image" class="block mt-1 w-full text-gray-900 dark:text-gray-100 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none" type="file" name="image" required />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        {{-- NEW: GALLERY UPLOAD (Optional on Create) --}}
                        <div class="mt-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h3 class="font-bold text-lg mb-2">Event Gallery</h3>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Photos (Optional)</label>
                            <input type="file" name="gallery[]" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" accept="image/*">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">You can select multiple files.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="start_at" :value="__('Start Date')" />
                                <x-text-input id="start_at" class="block mt-1 w-full" type="datetime-local" name="start_at" :value="old('start_at')" required />
                                <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_at" :value="__('End Date')" />
                                <x-text-input id="end_at" class="block mt-1 w-full" type="datetime-local" name="end_at" :value="old('end_at')" required />
                                <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
                            </div>
                        </div>

                        <div class="block mt-4">
                            <label for="is_pinned" class="inline-flex items-center">
                                <input id="is_pinned" type="checkbox" name="is_pinned" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Pin this event?') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Create Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
