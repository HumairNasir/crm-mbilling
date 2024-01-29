<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <h1 class="text-center text-2xl font-bold">Create new password</h3>
        <p class="text-center text-lg">Your new password must be different from previous one</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            {{-- <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div> --}}

            <!-- Password -->
            <div class="mt-4 relative">
                <x-label for="password" :value="__('Password')" class="text-lg text-black text-black-700" />

                <x-input id="password" class="block mt-1 w-full pl-40" type="password" name="password" required />
                <x-input id="email" class="block mt-1 w-full" type="hidden" name="email" :value="old('email', $request->email)" required autofocus />
            <div class="cursor-pointer ml-4" onclick="togglePasswordVisibility('password')">
                <svg class="right-svg absolute eye-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M2.03613 12.322C1.96712 12.1146 1.96712 11.8904 2.03613 11.683C3.42313 7.51 7.36013 4.5 12.0001 4.5C16.6381 4.5 20.5731 7.507 21.9631 11.678C22.0331 11.885 22.0331 12.109 21.9631 12.317C20.5771 16.49 16.6401 19.5 12.0001 19.5C7.36213 19.5 3.42613 16.493 2.03613 12.322Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
                <svg class="left-svgs absolute" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Frame">
                        <path id="Vector" d="M16.5 10.5V6.75C16.5 5.55653 16.0259 4.41193 15.182 3.56802C14.3381 2.72411 13.1935 2.25 12 2.25C10.8065 2.25 9.66193 2.72411 8.81802 3.56802C7.97411 4.41193 7.5 5.55653 7.5 6.75V10.5M6.75 21.75H17.25C17.8467 21.75 18.419 21.5129 18.841 21.091C19.2629 20.669 19.5 20.0967 19.5 19.5V12.75C19.5 12.1533 19.2629 11.581 18.841 11.159C18.419 10.7371 17.8467 10.5 17.25 10.5H6.75C6.15326 10.5 5.58097 10.7371 5.15901 11.159C4.73705 11.581 4.5 12.1533 4.5 12.75V19.5C4.5 20.0967 4.73705 20.669 5.15901 21.091C5.58097 21.5129 6.15326 21.75 6.75 21.75Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                </svg>
            </div>

            <!-- Confirm Password -->
            <div class="mt-4 relative">
                <x-label for="password" :value="__('Confirm Password')" class="text-lg text-black text-black-700" />

                <x-input id="password_confirmation" class="block mt-1 w-full pl-40" type="password" name="password_confirmation" required />
                <div class="cursor-pointer ml-4" onclick="togglePasswordVisibility('password_confirmation')">
                    <svg class="right-svg absolute eye-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M2.03613 12.322C1.96712 12.1146 1.96712 11.8904 2.03613 11.683C3.42313 7.51 7.36013 4.5 12.0001 4.5C16.6381 4.5 20.5731 7.507 21.9631 11.678C22.0331 11.885 22.0331 12.109 21.9631 12.317C20.5771 16.49 16.6401 19.5 12.0001 19.5C7.36213 19.5 3.42613 16.493 2.03613 12.322Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <svg class="left-svgs absolute" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Frame">
                        <path id="Vector" d="M16.5 10.5V6.75C16.5 5.55653 16.0259 4.41193 15.182 3.56802C14.3381 2.72411 13.1935 2.25 12 2.25C10.8065 2.25 9.66193 2.72411 8.81802 3.56802C7.97411 4.41193 7.5 5.55653 7.5 6.75V10.5M6.75 21.75H17.25C17.8467 21.75 18.419 21.5129 18.841 21.091C19.2629 20.669 19.5 20.0967 19.5 19.5V12.75C19.5 12.1533 19.2629 11.581 18.841 11.159C18.419 10.7371 17.8467 10.5 17.25 10.5H6.75C6.15326 10.5 5.58097 10.7371 5.15901 11.159C4.73705 11.581 4.5 12.1533 4.5 12.75V19.5C4.5 20.0967 4.73705 20.669 5.15901 21.091C5.58097 21.5129 6.15326 21.75 6.75 21.75Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                </svg>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
