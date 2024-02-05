<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="#">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <h1 class="text-center text-2xl font-bold">Kriss Crm</h3>
        <p class="text-center text-lg">Sign In</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="relative">
                <x-label for="email" :value="__('Email')" class="text-lg text-black text-black-700" />

                <x-input id="email" class="block mt-1 w-full pl-40" placeholder="Mikemorgan@gmail.com" type="email" name="email" :value="old('email')" required autofocus />
                <svg class="left-svgs absolute" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M21.75 6.75V17.25C21.75 17.8467 21.5129 18.419 21.091 18.841C20.669 19.2629 20.0967 19.5 19.5 19.5H4.5C3.90326 19.5 3.33097 19.2629 2.90901 18.841C2.48705 18.419 2.25 17.8467 2.25 17.25V6.75M21.75 6.75C21.75 6.15326 21.5129 5.58097 21.091 5.15901C20.669 4.73705 20.0967 4.5 19.5 4.5H4.5C3.90326 4.5 3.33097 4.73705 2.90901 5.15901C2.48705 5.58097 2.25 6.15326 2.25 6.75M21.75 6.75V6.993C21.75 7.37715 21.6517 7.75491 21.4644 8.0903C21.2771 8.42569 21.0071 8.70754 20.68 8.909L13.18 13.524C12.8252 13.7425 12.4167 13.8582 12 13.8582C11.5833 13.8582 11.1748 13.7425 10.82 13.524L3.32 8.91C2.99292 8.70854 2.72287 8.42669 2.53557 8.0913C2.34827 7.75591 2.24996 7.37815 2.25 6.994V6.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <!-- Password -->
            <div class="mt-4 relative">
                <x-label for="password" :value="__('Password')" class="text-lg text-black text-black-700" />

                <x-input id="password" placeholder="Password" class="block mt-1 w-full pl-40" type="password" name="password" required autocomplete="current-password" />
                <svg class="left-svgs absolute" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Frame">
                        <path id="Vector" d="M16.5 10.5V6.75C16.5 5.55653 16.0259 4.41193 15.182 3.56802C14.3381 2.72411 13.1935 2.25 12 2.25C10.8065 2.25 9.66193 2.72411 8.81802 3.56802C7.97411 4.41193 7.5 5.55653 7.5 6.75V10.5M6.75 21.75H17.25C17.8467 21.75 18.419 21.5129 18.841 21.091C19.2629 20.669 19.5 20.0967 19.5 19.5V12.75C19.5 12.1533 19.2629 11.581 18.841 11.159C18.419 10.7371 17.8467 10.5 17.25 10.5H6.75C6.15326 10.5 5.58097 10.7371 5.15901 11.159C4.73705 11.581 4.5 12.1533 4.5 12.75V19.5C4.5 20.0967 4.73705 20.669 5.15901 21.091C5.58097 21.5129 6.15326 21.75 6.75 21.75Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                </svg>
                <div class="cursor-pointer ml-4" onclick="togglePasswordVisibility('password')">
                    <svg class="right-svg absolute eye-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M2.03613 12.322C1.96712 12.1146 1.96712 11.8904 2.03613 11.683C3.42313 7.51 7.36013 4.5 12.0001 4.5C16.6381 4.5 20.5731 7.507 21.9631 11.678C22.0331 11.885 22.0331 12.109 21.9631 12.317C20.5771 16.49 16.6401 19.5 12.0001 19.5C7.36213 19.5 3.42613 16.493 2.03613 12.322Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg class="right-svg absolute eye-hide" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3.97959 8.223C3.04405 9.32718 2.34741 10.6132 1.93359 12C3.22559 16.338 7.24359 19.5 11.9996 19.5C12.9926 19.5 13.9526 19.362 14.8626 19.105M6.22759 6.228C7.94024 5.09786 9.94768 4.49688 11.9996 4.5C16.7556 4.5 20.7726 7.662 22.0646 11.998C21.3566 14.3673 19.8366 16.4116 17.7716 17.772M6.22759 6.228L2.99959 3M6.22759 6.228L9.87759 9.878M17.7716 17.772L20.9996 21M17.7716 17.772L14.1216 14.122C14.4002 13.8434 14.6212 13.5127 14.772 13.1486C14.9227 12.7846 15.0003 12.3945 15.0003 12.0005C15.0003 11.6065 14.9227 11.2164 14.772 10.8524C14.6212 10.4883 14.4002 10.1576 14.1216 9.879C13.843 9.6004 13.5122 9.3794 13.1482 9.22863C12.7842 9.07785 12.3941 9.00025 12.0001 9.00025C11.6061 9.00025 11.216 9.07785 10.8519 9.22863C10.4879 9.3794 10.1572 9.6004 9.87859 9.879M14.1206 14.121L9.87959 9.88" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 float-right" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-3 sign_in">
                    {{ __('Sign In') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
