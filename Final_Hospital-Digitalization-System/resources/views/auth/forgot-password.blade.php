@extends('layouts.auth-layout')

@section('title', 'Forgot Password')

@section('content')
    <div class="flex justify-center mt-12">
        <div class="w-full max-w-md px-6 py-8 bg-white shadow-md rounded-lg space-y-6">
            <div class="text-center text-2xl font-semibold text-gray-800">
                {{ __('Forgot your password?') }}
            </div>
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="w-full transform text-gray-800 border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                    <input type="text" for="email" id="email" name="email" placeholder="Email"
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required autofocus autocomplete="off" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-500 hover:underline transition-all duration-300">
                        {{ __('Back to Login') }}
                    </a>

                    <x-primary-button>
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection