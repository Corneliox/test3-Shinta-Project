<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. UPDATE INFO --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. ARTIST PROFILE PICTURE (WITH ROTATION) --}}
            @if(auth()->user()->is_artist)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Artist Profile Picture') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Update your public artist photo.") }}
                            </p>
                        </header>

                        {{-- Make sure your route matches web.php --}}
                        <form method="POST" action="{{ route('artist-profile.update-picture') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            {{-- PREVIEW & ROTATE UI --}}
                            <div class="mb-4">
                                <div class="mb-3 text-center p-3 bg-gray-50 border rounded position-relative" style="min-height: 220px;">
                                    
                                    {{-- Image Display --}}
                                    <div class="d-inline-block position-relative">
                                        @if(auth()->user()->artistProfile && auth()->user()->artistProfile->profile_picture)
                                            <img id="profilePreview" src="{{ Storage::url(auth()->user()->artistProfile->profile_picture) }}" 
                                                 class="rounded-full shadow-sm" 
                                                 style="width: 180px; height: 180px; object-fit: cover; transition: transform 0.3s ease;">
                                        @else
                                            <img id="profilePreview" src="#" 
                                                 class="rounded-full shadow-sm" 
                                                 style="width: 180px; height: 180px; object-fit: cover; display: none; transition: transform 0.3s ease;">
                                            <p id="profilePlaceholderText" class="text-muted mt-5">No image selected</p>
                                        @endif
                                    </div>

                                    {{-- Rotate Button --}}
                                    <button type="button" id="btnRotateProfile" class="absolute bottom-4 right-4 bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 shadow" title="Rotate 90° Right" style="z-index: 10;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </div>

                                <input type="file" name="profile_picture" id="profileInput" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" accept="image/*" onchange="previewProfileFile(this)">
                                
                                {{-- HIDDEN ROTATION INPUT --}}
                                <input type="hidden" name="rotation" id="profileRotationInput" value="0">
                                
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Photo') }}</x-primary-button>
                                @if (session('status') === 'profile-picture-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 3. GATEKEEPER (One Gate) --}}
            @if(auth()->user()->is_admin || auth()->user()->is_superadmin)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('One Gate Policy') }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __("Set the WhatsApp order receiver.") }}</p>
                        </header>
                        <div class="mt-6">
                            @if(auth()->user()->is_shop_contact)
                                <x-primary-button disabled class="bg-green-600 cursor-default opacity-100">
                                    {{ __('You are the Active Gatekeeper') }}
                                </x-primary-button>
                            @else
                                <form method="POST" action="{{ route('profile.set-gate') }}">
                                    @csrf
                                    <x-primary-button>{{ __('Make me the Gatekeeper') }}</x-primary-button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. PASSWORD & DELETE --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">@include('profile.partials.update-password-form')</div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">@include('profile.partials.delete-user-form')</div>
            </div>
        </div>
    </div>

    {{-- JS FOR PROFILE ROTATION --}}
    @if(auth()->user()->is_artist)
    <script>
        let profileRotation = 0;
        const btnRotateProfile = document.getElementById('btnRotateProfile');
        const profilePreview = document.getElementById('profilePreview');
        const profileRotationInput = document.getElementById('profileRotationInput');
        const profilePlaceholderText = document.getElementById('profilePlaceholderText');

        if(btnRotateProfile) {
            btnRotateProfile.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submit
                profileRotation = (profileRotation + 90) % 360;
                profilePreview.style.transform = `rotate(${profileRotation}deg)`;
                profileRotationInput.value = profileRotation;
            });
        }

        function previewProfileFile(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    profilePreview.style.display = 'block';
                    if(profilePlaceholderText) profilePlaceholderText.style.display = 'none';
                    
                    // Reset
                    profileRotation = 0;
                    profileRotationInput.value = 0;
                    profilePreview.style.transform = 'rotate(0deg)';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
    @endif
</x-app-layout>