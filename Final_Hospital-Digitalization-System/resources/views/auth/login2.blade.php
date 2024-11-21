@extends('layouts.auth-layout')

@section('title', 'Log In')

@section('content')
<section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
    <div class="text-center">
        <img src="{{ asset('/images/hospitalLogo.png') }}" alt="Hospital Logo" class="w-40 h-40 mx-auto mb-4">
    </div>
    <div class="text-center text-4xl font-medium">Log In</div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-green-500">
            {{ session('status') }}
        </div>
    @endif

    <!-- Form Login -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address or Username -->
        <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
            <input type="text" id="user_id" name="user_id" placeholder="Email or Username"
                class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                value="{{ old('user_id') ?? '' }}" required autofocus autocomplete="off">
        </div>

        <!-- Password -->
        <div class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
            <input type="password" id="password" name="password" placeholder="Password"
                class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none pr-10"
                required autocomplete="off" onpaste="return false;">

            <!-- Eye Icon -->
            <span class="absolute right-0 top-1/2 transform -translate-y-1/2 cursor-pointer pr-2" onclick="togglePasswordVisibility()">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" />
                </svg>
            </span>
            
            @error('password')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Button Login -->
        <div class="flex items-center justify-between mt-8">
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-500 hover:underline transition-all duration-300" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" id="loginForm" class="transform rounded-sm bg-indigo-500 py-2 px-6 font-bold duration-300 hover:bg-indigo-700">
                LOG IN
            </button>
        </div>
    </form>

    <!-- Create Account Link -->
    <p class="text-center text-lg mt-6">
        No account?
        <a href="{{ route('register') }}" class="font-medium text-indigo-500 underline-offset-4 hover:underline">Create One</a>
    </p>

    <div class="text-transparent text-center" id="a">
        a
        @error('user_id')
            <div class="text-red-500" id="error-msg">{{ $message }}</div>
        @enderror
    </div>
</section>

<script>
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        var button = document.getElementById("loginForm");
        button.disabled = true;
    });

    @if ($errors->has('user_id'))
        document.getElementById('a').innerHTML = document.getElementById('error-msg').innerHTML;
        document.getElementById('a').classList.remove('text-transparent');
        document.getElementById('a').classList.add('text-red-500');
    @endif

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `<path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" />`;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `<path d="M2.458 10c.732-2.89 3.945-6 7.542-6s6.81 3.11 7.542 6c-.732 2.89-3.945 6-7.542 6S3.19 12.89 2.458 10zM10 13c-1.656 0-3-1.344-3-3s1.344-3 3-3 3 1.344 3 3-1.344 3-3 3zm0-4.5c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5z" />`;
        }
    }
</script>
@endsection