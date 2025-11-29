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

            {{-- 2. GATEKEEPER SETTING (ADMIN ONLY) --}}
            {{-- This is the new section for the "One Gate" Logic --}}
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
</x-app-layout>