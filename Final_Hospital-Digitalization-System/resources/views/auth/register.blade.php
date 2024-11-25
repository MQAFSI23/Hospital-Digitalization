@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
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
                <x-input-label for="name" :value="__('Nama')"  />
                <input type="text" id="name" name="name" placeholder="Name"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('name') }}" required autofocus autocomplete="off">
            </div>
            @error('name')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tanggal Lahir -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')"  />
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tanggal_lahir') }}" required>
            </div>
            @error('tanggal_lahir')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Jenis Kelamin -->
            <div class="w-full mt-6">
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')"  />
                <select id="jenis_kelamin" name="jenis_kelamin" class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500" required>
                    <option value="pria" {{ old('jenis_kelamin') == 'pria' ? 'selected' : '' }}>Pria</option>
                    <option value="wanita" {{ old('jenis_kelamin') == 'wanita' ? 'selected' : '' }}>Wanita</option>
                </select>
            </div>
            @error('jenis_kelamin')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Berat Badan -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="berat_badan" :value="__('Berat Badan (kg)')" />
                <input type="number" id="berat_badan" name="berat_badan" placeholder="Berat Badan (kg)"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('berat_badan') }}" step="0.1" min="0" required autocomplete="off">
            </div>
            @error('berat_badan')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tinggi Badan -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="tinggi_badan" :value="__('Tinggi Badan (cm)')" />
                <input type="number" id="tinggi_badan" name="tinggi_badan" placeholder="Tinggi Badan (cm)"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tinggi_badan') }}" step="0.1" min="0" required autocomplete="off">
            </div>
            @error('tinggi_badan')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Username -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="username" :value="__('Username')"  />
                <input type="text" id="username" name="username" placeholder="Username"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('username') }}" required autocomplete="off">
            </div>
            @error('username')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Email Address -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="email" :value="__('Email')" />
                <input type="email" id="email" name="email" placeholder="Email"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('email') }}" required autocomplete="off">
            </div>
            @error('email')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Password -->
            <div class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="password" :value="__('Password')" />
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    required autocomplete="off" onpaste="return false;">
            </div>
            @error('password')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Confirm Password -->
            <div class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    required autocomplete="off" onpaste="return false;">
            </div>
            @error('password_confirmation')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-8">
                <!-- Link Already Registered -->
                <a class="text-sm font-medium text-indigo-500 hover:underline transition-all duration-300" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <!-- Register Button -->
                <button type="submit" id="registerForm" class="transform rounded-sm bg-indigo-500 py-2 px-2 font-bold duration-300 hover:bg-indigo-700">
                    Register
                </button>
            </div>
        </form>
    </section>

    <script>
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            var button = document.getElementById("registerForm");
            button.disabled = true;
        });
    </script>
@endsection