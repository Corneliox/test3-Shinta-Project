<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. UPDATE PROFILE INFO (Name, Email, Phone) --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" id="update-profile-information">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 1.5 ARTIST PROFILE PICTURE (New Section for Rotation) --}}
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

                        <form method="POST" action="{{ route('artist-profile.update-picture') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            {{-- PREVIEW & ROTATE --}}
                            <div class="mb-4">
                                <div class="mb-3 text-center p-3 bg-gray-50 border rounded position-relative" style="min-height: 200px;">
                                    @if(auth()->user()->artistProfile && auth()->user()->artistProfile->profile_picture)
                                        <img id="profilePreview" src="{{ Storage::url(auth()->user()->artistProfile->profile_picture) }}" 
                                             class="rounded-full shadow-sm mx-auto" 
                                             style="max-height: 200px; width: 200px; object-fit: cover; transition: transform 0.3s ease;">
                                    @else
                                        <img id="profilePreview" src="#" 
                                             class="rounded-full shadow-sm mx-auto" 
                                             style="max-height: 200px; width: 200px; object-fit: cover; display: none; transition: transform 0.3s ease;">
                                        <p id="profilePlaceholderText" class="text-muted mt-5">No image selected</p>
                                    @endif

                                    {{-- Rotate Button --}}
                                    <button type="button" id="btnRotateProfile" class="btn btn-secondary btn-sm position-absolute bottom-0 right-0 m-3 shadow" title="Rotate 90° Right">
                                        <i class="bi-arrow-clockwise"></i> Rotate
                                    </button>
                                </div>

                                <input type="file" name="profile_picture" id="profileInput" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" accept="image/*" onchange="previewProfileFile(this)">
                                <input type="hidden" name="rotation" id="profileRotationInput" value="0">
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Photo') }}</x-primary-button>
                                @if (session('status') === 'profile-picture-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 2. GATEKEEPER SETTING (ADMIN ONLY) --}}
            @if(auth()->user()->is_admin || auth()->user()->is_superadmin)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('One Gate Policy Setting') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __("Determine which Admin receives WhatsApp orders. Please ensure you have saved your Phone Number above first.") }}
                                </p>
                            </header>

                            <div class="mt-6">
                                @if(auth()->user()->is_shop_contact)
                                    <div class="flex items-center gap-4">
                                        <x-primary-button disabled class="bg-green-600 hover:bg-green-500 focus:bg-green-500 active:bg-green-600 cursor-default">
                                            <i class="bi bi-check-circle-fill me-2"></i> {{ __('You are the Active Gatekeeper') }}
                                        </x-primary-button>
                                        <span class="text-sm text-green-600 dark:text-green-400">
                                            {{ __('Orders are being sent to your WhatsApp.') }}
                                        </span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('profile.set-gate') }}">
                                        @csrf
                                        <div class="flex items-center gap-4">
                                            <x-primary-button>
                                                {{ __('Make me the Gatekeeper') }}
                                            </x-primary-button>
                                            
                                            @if(\App\Models\User::where('is_shop_contact', true)->exists())
                                                <p class="text-sm text-amber-600 dark:text-amber-400">
                                                    {{ __('Warning: Another admin is currently the gatekeeper.') }}
                                                </p>
                                            @else
                                                <p class="text-sm text-red-600 dark:text-red-400">
                                                    {{ __('No gatekeeper set! Orders are using the default number.') }}
                                                </p>
                                            @endif
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>
            @endif

            {{-- 3. UPDATE PASSWORD --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" id="update-password-information">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 4. DELETE ACCOUNT --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS FOR PROFILE ROTATION --}}
    @if(auth()->user()->is_artist)
    <script>
        let profileRotation = 0;
        const btnRotateProfile = document.getElementById('btnRotateProfile');
        const profilePreview = document.getElementById('profilePreview');
        const profileRotationInput = document.getElementById('profileRotationInput');
        const profilePlaceholderText = document.getElementById('profilePlaceholderText');

        if(btnRotateProfile) {
            btnRotateProfile.addEventListener('click', function() {
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