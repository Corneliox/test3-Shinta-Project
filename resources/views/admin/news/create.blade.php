<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Write New Article') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Article Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter title..." required>
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Thumbnail --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Main Thumbnail (Card Image)</label>
                        <input type="file" name="thumbnail" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" accept="image/*" required>
                        @error('thumbnail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- THE EDITOR --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Content</label>
                        {{-- Tip: Use the 'Table' button to create Text | Image layouts --}}
                        <textarea id="news-editor" name="content" rows="20">{{ old('content') }}</textarea>
                        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Publish Toggle --}}
                    <div class="mb-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">Publish Immediately</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Post Article') }}</x-primary-button>
                        <a href="{{ route('admin.news.index') }}" class="text-gray-600 hover:text-gray-900 underline dark:text-gray-400">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FORCE LOAD: Use Cloudflare CDN instead of Tiny.cloud (No API Key needed) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
      tinymce.init({
        selector: '#news-editor',
        
        // Force Base URL to avoid relative path errors
        document_base_url: '{{ url("/") }}',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,

        // DARK MODE THEME (Standardized for CDN)
        skin: 'oxide-dark',
        content_css: 'dark',

        plugins: 'image link media table lists code preview wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
        
        // Image Upload Logic
        images_upload_url: '{{ route("admin.news.editor.upload") }}',
        automatic_uploads: true,
        file_picker_types: 'image',
        
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route("admin.news.editor.upload") }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                const json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                resolve(json.location);
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }),

        image_dimensions: true,
        image_class_list: [
            {title: 'Responsive', value: 'img-fluid'},
            {title: 'None', value: ''}
        ],
        
        height: 600,
        
        // Fallback: If styling fails, show the editor anyway
        init_instance_callback: function (editor) {
            editor.on('ExecCommand', function (e) {
                console.log('Command executed: ' + e.command);
            });
        }
      });
    </script>
</x-app-layout>