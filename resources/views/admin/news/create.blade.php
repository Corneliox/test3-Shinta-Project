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

    /* ===== URL FIXES ===== */
    document_base_url: '{{ url("/") }}/',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,

    /* ===== DARK MODE ===== */
    skin: 'oxide-dark',
    content_css: 'dark',

    /* ===== WORD-LIKE FEATURES ===== */
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image',
        'charmap', 'preview', 'anchor', 'searchreplace',
        'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'wordcount'
    ],

    toolbar: `
        undo redo | blocks fontfamily fontsize |
        bold italic underline strikethrough |
        alignleft aligncenter alignright alignjustify |
        bullist numlist outdent indent |
        table image media link |
        removeformat code
    `,

    menubar: 'file edit view insert format tools table help',

    /* ===== TABLE POWER (THIS IS KEY) ===== */
    table_default_attributes: {
        border: '0'
    },
    table_default_styles: {
        width: '100%'
    },

    table_toolbar: `
        tableprops tabledelete |
        tableinsertrowbefore tableinsertrowafter |
        tabledeleterow |
        tableinsertcolbefore tableinsertcolafter |
        tabledeletecol
    `,

    /* ===== IMAGE HANDLING ===== */
    image_dimensions: true,
    image_advtab: true,
    image_caption: true,

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
            if (!json.location) {
                reject('Invalid response');
                return;
            }
            resolve(json.location);
        };

        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }),

    /* ===== VISUAL QUALITY ===== */
    height: 600,
    resize: true,

    /* ===== TABLE LAYOUT TEMPLATES (🔥 IMPORTANT) ===== */
    table_templates: [
        {
            title: 'Text | Image',
            description: 'Two columns: text left, image right',
            content:
                '<table width="100%"><tr>' +
                '<td width="70%">Write your text here...</td>' +
                '<td width="30%"><p>Insert image here</p></td>' +
                '</tr></table>'
        },
        {
            title: 'Image | Text',
            description: 'Two columns: image left, text right',
            content:
                '<table width="100%"><tr>' +
                '<td width="30%"><p>Insert image here</p></td>' +
                '<td width="70%">Write your text here...</td>' +
                '</tr></table>'
        },
        {
            title: 'Full Text',
            description: 'Single column text',
            content:
                '<table width="100%"><tr>' +
                '<td>Write your text here...</td>' +
                '</tr></table>'
        }
    ]
});
</script>

</x-app-layout>