<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Account Setup Pending') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md border dark:border-gray-700 text-center space-y-6">
                
                <!-- Icon -->
                <div class="flex justify-center">
                    <span class="p-4 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-full animate-pulse">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>

                <!-- Messaging -->
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Profile Setup Required</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $message }}
                    </p>
                </div>

                <!-- Informational Note -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-xs text-gray-400 text-left space-y-1">
                    <strong class="text-gray-600 dark:text-gray-300 block uppercase">Sandbox Note:</strong>
                    <p>To log in and see fully functional portals immediately, please log out and use one of the pre-seeded sandbox accounts, such as:</p>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        <li><strong>Admin:</strong> admin@university.com</li>
                        <li><strong>Lecturer:</strong> charles@university.com</li>
                        <li><strong>Student:</strong> alice@university.com</li>
                    </ul>
                    <p class="mt-1">All accounts use the password: <strong>password</strong></p>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3 pt-4 border-t dark:border-gray-700">
                    <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                        Profile Settings
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Log Out
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
