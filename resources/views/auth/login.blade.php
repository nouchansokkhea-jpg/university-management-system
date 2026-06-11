<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            @if (Route::has('register'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                    {{ __("Register Account") }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <x-slot name="outsideCard">
        <div class="px-6 py-5 bg-white dark:bg-gray-800 shadow-md rounded-lg border border-indigo-50 dark:border-indigo-950">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 text-center">
                ⚡ Demo Quick Login
            </h3>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="quickLogin('admin@university.com')" class="flex flex-col items-center justify-center p-2.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 rounded-lg border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition duration-150 text-center font-semibold">
                    <span class="font-bold">Super Admin</span>
                    <span class="opacity-90 text-[10px] mt-0.5">admin@university.com</span>
                </button>
                <button type="button" onclick="quickLogin('registrar@university.com')" class="flex flex-col items-center justify-center p-2.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-200 rounded-lg border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition duration-150 text-center font-semibold">
                    <span class="font-bold">Registrar</span>
                    <span class="opacity-90 text-[10px] mt-0.5">registrar@university.com</span>
                </button>
                <button type="button" onclick="quickLogin('charles@university.com')" class="flex flex-col items-center justify-center p-2.5 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 rounded-lg border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition duration-150 text-center font-semibold">
                    <span class="font-bold">Lecturer</span>
                    <span class="opacity-90 text-[10px] mt-0.5">charles@university.com</span>
                </button>
                <button type="button" onclick="quickLogin('alice@university.com')" class="flex flex-col items-center justify-center p-2.5 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 rounded-lg border border-emerald-250 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition duration-150 text-center font-semibold">
                    <span class="font-bold">Student</span>
                    <span class="opacity-90 text-[10px] mt-0.5">alice@university.com</span>
                </button>
            </div>
            <div class="mt-3 text-center text-[10px] text-gray-500 dark:text-gray-400">
                Password for all demo accounts is <span class="font-semibold text-gray-600 dark:text-gray-300">password</span>
            </div>
        </div>
    </x-slot>

    <script>
        function quickLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
            document.querySelector('form').submit();
        }
    </script>
</x-guest-layout>
