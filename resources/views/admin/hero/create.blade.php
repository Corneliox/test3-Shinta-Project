<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload Hero Images') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.hero.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Select Images</label>
                        
                        {{-- DRAG & DROP ZONE --}}
                        <div id="drop-zone" class="w-full min-h-[200px] border-2 border-dashed border-gray-300 rounded-lg flex flex-col justify-center items-center cursor-pointer hover:border-blue-500 hover:bg-gray-50 transition-all relative">
                            
                            {{-- Hidden Native Input (Note: name="images[]" and multiple) --}}
                            <input type="file" name="images[]" id="file-input" class="hidden" accept="image/*" multiple required>
                            
                            {{-- Default Content --}}
                            <div id="drop-zone-text" class="text-center p-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">
                                    <span class="text-blue-600 hover:underline">Click to browse</span> or drag multiple files here
                                </p>
                                <p class="text-xs text-gray-500 mt-1">SVG, PNG, JPG or GIF (Max 5MB each)</p>
                            </div>

                            {{-- PREVIEW GRID --}}
                            <div id="preview-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 w-full p-4 hidden">
                                {{-- Thumbnails will be injected here via JS --}}
                            </div>

                        </div>
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.hero.index') }}" class="text-gray-500 underline mr-4 mt-2">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-md transition transform hover:scale-105">
                            Upload All
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ADVANCED MULTI-FILE SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const dropZoneText = document.getElementById('drop-zone-text');
            const previewGrid = document.getElementById('preview-grid');
            
            // This object holds the files basically "in memory" before submitting
            const dataTransfer = new DataTransfer();

            // 1. Handle Click
            dropZone.addEventListener('click', (e) => {
                // Don't trigger if clicking a remove button
                if(e.target.closest('.remove-btn')) return;
                fileInput.click();
            });

            // 2. Handle File Selection (Standard Click)
            fileInput.addEventListener('change', () => {
                handleFiles(fileInput.files);
            });

            // 3. Drag Events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Highlight Logic
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('border-blue-500', 'bg-blue-50'), false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-blue-500', 'bg-blue-50'), false);
            });

            // 4. Handle Drop
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                handleFiles(dt.files);
            });

            // --- CORE LOGIC ---
            function handleFiles(files) {
                // Add new files to our master list
                for (let i = 0; i < files.length; i++) {
                    // Optional: Check if file already exists to prevent duplicates?
                    // For now, we assume admin knows what they are doing.
                    dataTransfer.items.add(files[i]);
                }

                // Sync with the actual input
                fileInput.files = dataTransfer.files;

                // Render UI
                renderPreviews();
            }

            function renderPreviews() {
                // Toggle visibility
                if (dataTransfer.files.length > 0) {
                    dropZoneText.classList.add('hidden');
                    previewGrid.classList.remove('hidden');
                } else {
                    dropZoneText.classList.remove('hidden');
                    previewGrid.classList.add('hidden');
                }

                // Clear current grid
                previewGrid.innerHTML = '';

                // Loop through all files in DataTransfer
                Array.from(dataTransfer.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onloadend = function() {
                        
                        const div = document.createElement('div');
                        div.className = "relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm aspect-video bg-gray-100";
                        
                        div.innerHTML = `
                            <img src="${reader.result}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" class="remove-btn bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition" data-index="${index}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <span class="absolute bottom-1 left-2 text-xs text-white drop-shadow-md truncate max-w-[90%]">${file.name}</span>
                        `;
                        
                        previewGrid.appendChild(div);
                    }
                });
            }

            // 5. Handle Removal
            previewGrid.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-btn');
                if (!btn) return;

                const indexToRemove = parseInt(btn.dataset.index);
                
                // Create a NEW DataTransfer to rebuild the list without the deleted item
                const newDataTransfer = new DataTransfer();
                
                Array.from(dataTransfer.files).forEach((file, i) => {
                    if (i !== indexToRemove) {
                        newDataTransfer.items.add(file);
                    }
                });

                // Update the master list
                dataTransfer.items.clear();
                Array.from(newDataTransfer.files).forEach(file => dataTransfer.items.add(file));

                // Sync Input
                fileInput.files = dataTransfer.files;

                // Re-render
                renderPreviews();
            });
        });
    </script>
</x-app-layout>