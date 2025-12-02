<x-guest-layout>
    {{-- FIX 1: Load Icons --}}
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">

    {{-- FIX 2: ADDED TITLE --}}
    <div class="mb-5 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ __('Register') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Create a new account.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-14"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                
                <span class="absolute inset-y-0 right-0 flex items-center justify-center cursor-pointer text-gray-400 hover:text-gray-600"
                      style="width: 50px;"
                      onclick="togglePassword('password', 'reg-eye-1')">
                    <i id="reg-eye-1" class="bi bi-eye-slash text-xl"></i>
                </span>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-14"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <span class="absolute inset-y-0 right-0 flex items-center justify-center cursor-pointer text-gray-400 hover:text-gray-600"
                      style="width: 50px;"
                      onclick="togglePassword('password_confirmation', 'reg-eye-2')">
                    <i id="reg-eye-2" class="bi bi-eye-slash text-xl"></i>
                </span>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- FIX 3: IMPROVED BOTTOM LAYOUT (Flex Wrap for Mobile) --}}
        <div class="flex flex-col sm:flex-row items-center justify-end mt-6 gap-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = "password";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    </script>
</x-guest-layout>