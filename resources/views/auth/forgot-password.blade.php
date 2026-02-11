forgotpasword.blade
<x-guest-layout>
    <x-auth-card  >
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <h1 class="text-center text-3xl font-bold text-white mb-2">Forgot Password</h1>
        <p class="text-center text-white mb-8 text-lg">Please enter your email. You will receive a link to create a new password</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-6 pt-3">
                <x-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-300 mb-2" />
                <div class="relative" style="position: relative;">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
                         style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); z-index: 10; pointer-events: none;"
                         xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M21.75 6.75V17.25C21.75 17.8467 21.5129 18.419 21.091 18.841C20.669 19.2629 20.0967 19.5 19.5 19.5H4.5C3.90326 19.5 3.33097 19.2629 2.90901 18.841C2.48705 18.419 2.25 17.8467 2.25 17.25V6.75M21.75 6.75C21.75 6.15326 21.5129 5.58097 21.091 5.15901C20.669 4.73705 20.0967 4.5 19.5 4.5H4.5C3.90326 4.5 3.33097 4.73705 2.90901 5.15901C2.48705 5.58097 2.25 6.15326 2.25 6.75M21.75 6.75V6.993C21.75 7.37715 21.6517 7.75491 21.4644 8.0903C21.2771 8.42569 21.0071 8.70754 20.68 8.909L13.18 13.524C12.8252 13.7425 12.4167 13.8582 12 13.8582C11.5833 13.8582 11.1748 13.7425 10.82 13.524L3.32 8.91C2.99292 8.70854 2.72287 8.42669 2.53557 8.0913C2.34827 7.75591 2.24996 7.37815 2.25 6.994V6.75" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <x-input id="email"
                             class="block w-full h-12 pl-11 pr-4 bg-gray-700 text-black border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                             style="position: relative; z-index: 1; padding-left: 2.75rem; padding-right: 1rem; background-color: #E0E0E0; color: #000; border-color: #4B5563;"
                             type="email" name="email" :value="old('email')" required autofocus
                             placeholder="Mikemorgan@gmail.com" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end mb-6 pt-4">
                <x-button class="h-12 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg px-8 py-2 transition duration-200 ease-in-out shadow-lg">
                    {{ __('Send Email') }}
                </x-button>
            </div>

            <p class="text-center text-sm text-gray-300 mt-3">
                Remember your password?
                <a class="text-white hover:text-blue-400 underline hover:no-underline transition-colors duration-200 ml-1" href="{{ route('login') }}">
                    Sign In
                </a>
            </p>
        </form>
    </x-auth-card>
</x-guest-layout>