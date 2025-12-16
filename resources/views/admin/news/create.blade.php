@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Write New Article</h2>

            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block font-bold mb-2">Article Title</label>
                    <input type="text" name="title" class="w-full rounded border-gray-300" required>
                </div>

                {{-- Thumbnail --}}
                <div class="mb-4">
                    <label class="block font-bold mb-2">Main Thumbnail (Card Image)</label>
                    <input type="file" name="thumbnail" class="w-full border p-2 rounded" accept="image/*" required>
                </div>

                {{-- THE WORD-LIKE EDITOR --}}
                <div class="mb-4">
                    <label class="block font-bold mb-2">Content</label>
                    <textarea id="news-editor" name="content" rows="20"></textarea>
                </div>

                {{-- Publish Toggle --}}
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm">
                        <span class="ml-2">Publish Immediately</span>
                    </label>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    Post Article
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ADVANCED TINYMCE CONFIGURATION --}}
<script src="https://cdn.tiny.cloud/1/w0mxt01iygm8l26kqy3w3okjhxfjp66y9mpfory164br98jq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#news-editor',
    plugins: 'image link media table lists code preview wordcount',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
    
    // Enable Image Uploads
    images_upload_url: '{{ route("admin.news.editor.upload") }}',
    automatic_uploads: true,
    file_picker_types: 'image',
    
    // Image Upload Handler Logic
    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '{{ route("admin.news.editor.upload") }}');
        
        // Add CSRF Token
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

    // Enable Resizing Images inside Editor
    image_dimensions: true,
    image_class_list: [
        {title: 'Responsive', value: 'img-fluid'},
        {title: 'None', value: ''}
    ],
    
    height: 600
  });
</script>
@endsection