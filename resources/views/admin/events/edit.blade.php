<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $event->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            {{-- ADDED 'rich-editor' class here --}}
                            <textarea id="description" name="description" class="rich-editor block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $event->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- MAIN IMAGE --}}
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('Main Event Image (Optional)')" />
                            <div class="mt-2">
                                {{-- For Edit Page: Show existing image if available --}}
                                @if(isset($event) && $event->image_path)
                                    <img id="eventPreview" src="{{ Storage::url($event->image_path) }}" class="mx-auto rounded shadow-sm" style="max-height: 300px; transition: transform 0.3s ease;">
                                @else
                                    <img id="eventPreview" src="#" class="mx-auto rounded shadow-sm" style="max-height: 300px; display: none; transition: transform 0.3s ease;">
                                    <p id="eventPlaceholder" class="text-gray-500 mt-10">No image selected</p>
                                @endif

                                <button type="button" id="btnRotateEvent" class="absolute bottom-4 right-4 bg-gray-800 text-white px-3 py-1 rounded hover:bg-gray-700 shadow" title="Rotate">
                                    Rotate ↻
                                </button>
                            </div>
                            <input id="image" class="block mt-2 w-full text-gray-900 dark:text-gray-100 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none" type="file" name="image" />
                            <small class="text-gray-500">Leave blank to keep the current image.</small>
                            <small class="text-danger">Make sure the image is Oriented</small>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        {{-- NEW: GALLERY UPLOAD SECTION --}}
                        <div class="mt-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h3 class="font-bold text-lg mb-2">Event Gallery</h3>
                            
                            {{-- 1. Upload Input --}}
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Add More Photos</label>
                            <input type="file" name="gallery[]" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" accept="image/*">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">You can select multiple files at once.</p>

                            {{-- 2. Download Button (Only shows if images exist) --}}
                            @if($event->images && $event->images->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Total Photos: <strong>{{ $event->images->count() }}</strong></p>
                                    <a href="{{ route('admin.events.download', $event->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Download All Photos (.zip)
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="start_at" :value="__('Start Date')" />
                                <x-text-input id="start_at" class="block mt-1 w-full" type="datetime-local" name="start_at" :value="old('start_at', $event->start_at->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_at" :value="__('End Date')" />
                                <x-text-input id="end_at" class="block mt-1 w-full" type="datetime-local" name="end_at" :value="old('end_at', $event->end_at->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
                            </div>
                        </div>

                        <div class="block mt-4">
                            <label for="is_pinned" class="inline-flex items-center">
                                <input id="is_pinned" type="checkbox" name="is_pinned" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    @checked(old('is_pinned', $event->is_pinned)) >
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Pin this event?') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Update Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- JS for Event Page --}}
<script>
    let evtRotation = 0;
    document.getElementById('btnRotateEvent').addEventListener('click', function(e) {
        e.preventDefault();
        evtRotation = (evtRotation + 90) % 360;
        document.getElementById('eventPreview').style.transform = `rotate(${evtRotation}deg)`;
        document.getElementById('eventRotationInput').value = evtRotation;
    });

    function previewEventFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('eventPreview');
                img.src = e.target.result;
                img.style.display = 'block';
                if(document.getElementById('eventPlaceholder')) document.getElementById('eventPlaceholder').style.display = 'none';
                
                evtRotation = 0;
                document.getElementById('eventRotationInput').value = 0;
                img.style.transform = 'rotate(0deg)';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>