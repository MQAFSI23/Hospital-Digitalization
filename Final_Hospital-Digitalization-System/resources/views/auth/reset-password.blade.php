@extends('layouts.auth-layout')

@section('title', 'Reset Password')

@section('content')
    <div class="w-full max-w-md mx-auto">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none cursor-not-allowed"
                            type="email" name="email" :value="old('email', $request->email)" required autocomplete="email"
                            readonly />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <!-- Password -->
            <div class="mt-4 w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                            type="password" name="password" required autofocus autocomplete="new-password" onpaste="return false;" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4 w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                            type="password" name="password_confirmation" required autocomplete="new-password" onpaste="return false;" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button>
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection