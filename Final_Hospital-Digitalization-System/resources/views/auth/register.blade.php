@extends('layouts.auth-layout')

@section('content')
    <section class="flex w-[30rem] flex-col space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Register</div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-500">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Register -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <input type="text" id="name" name="name" placeholder="Name"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('name') }}" required autocomplete="off">
            </div>
            @error('name')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Username -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <input type="text" id="username" name="username" placeholder="Username"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('username') }}" required autocomplete="off">
            </div>
            @error('username')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Email Address -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <input type="email" id="email" name="email" placeholder="Email"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('email') }}" required autocomplete="off">
            </div>
            @error('email')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Password -->
            <div class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    required autocomplete="off" onpaste="return false;">
            </div>
            @error('password')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Confirm Password -->
            <div class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    required autocomplete="off" onpaste="return false;">
            </div>
            @error('password_confirmation')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-8">
                <!-- Link Already Registered -->
                <a class="text-sm font-medium text-indigo-500 hover:underline transition-all duration-300" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <!-- Register Button -->
                <button type="submit" id="registerButton" class="transform rounded-sm bg-indigo-500 py-2 px-6 font-bold duration-300 hover:bg-indigo-700">
                    Register
                </button>
            </div>
        </form>
    </section>

    <script>
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            var button = document.getElementById("registerButton");
            button.disabled = true;
        });
    </script>
@endsection