<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Article') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Article Title</label>
                        <input type="text" name="title" value="{{ old('title', $news->title) }}" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Update Thumbnail</label>
                        
                        @if($news->thumbnail)
                            <div class="mb-2">
                                <img src="{{ Storage::url($news->thumbnail) }}" class="h-32 rounded shadow-sm border p-1 bg-white">
                            </div>
                        @endif

                        <input type="file" name="thumbnail" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" accept="image/*">
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Leave empty to keep current image.</p>
                    </div>

                    {{-- THE EDITOR --}}
                    <div class="mb-4">
                        <label class="block font-bold mb-2 text-gray-700 dark:text-gray-300">Content</label>
                        <textarea id="news-editor" name="content" rows="20">{!! old('content', $news->content) !!}</textarea>
                    </div>

                    {{-- Publish Toggle --}}
                    <div class="mb-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ $news->is_published ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">Published</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Article') }}</x-primary-button>
                        <a href="{{ route('admin.news.index') }}" class="text-gray-600 hover:text-gray-900 underline dark:text-gray-400">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FORCE LOAD: Use Cloudflare CDN instead of Tiny.cloud (No API Key needed) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        if (!document.querySelector('#news-editor')) {
            console.error('TinyMCE: textarea #news-editor not found');
            return;
        }

        tinymce.init({
            selector: '#news-editor',

            /* disable telemetry */
            tinymce_cloud_reporting: false,

            /* URLs */
            document_base_url: '{{ url("/") }}/',
            relative_urls: false,

            /* UI */
            skin: 'oxide-dark',
            content_css: 'dark',
            height: 600,

            plugins: 'lists link image table code preview wordcount',
            toolbar: `
                undo redo | blocks |
                bold italic underline |
                alignleft aligncenter alignright |
                bullist numlist |
                table image link |
                code
            `,

            menubar: 'file edit view insert format tools table help',

            /* image upload */
            images_upload_url: '{{ route("admin.news.editor.upload") }}',
            automatic_uploads: true,
            file_picker_types: 'image',

            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("admin.news.editor.upload") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.onload = () => {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    const json = JSON.parse(xhr.responseText);
                    resolve(json.location);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }),

            table_templates: [
                {
                    title: 'Text | Image',
                    content:
                        '<table width="100%"><tr>' +
                        '<td width="70%">Text here</td>' +
                        '<td width="30%">Image here</td>' +
                        '</tr></table>'
                },
                {
                    title: 'Image | Text',
                    content:
                        '<table width="100%"><tr>' +
                        '<td width="30%">Image here</td>' +
                        '<td width="70%">Text here</td>' +
                        '</tr></table>'
                }
            ]
        });
    });
    </script>


</x-app-layout>