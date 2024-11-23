@extends('layouts.auth-layout')

@section('content')
    <section class="flex w-full max-w-full sm:w-[24rem] md:w-[30rem] lg:w-[40rem] px-4 flex-col space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Edit User</div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-500">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Edit User -->
        <form method="POST" action="{{ route('admin.updatePengguna', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- Username -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="username" :value="__('Username')" />
                <input type="text" id="username" name="username" placeholder="Username"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('username', $user->username) }}" required autocomplete="off">
            </div>
            @error('username')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Name -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="name" :value="__('Nama')" />
                <input type="text" id="name" name="name" placeholder="Name"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="off">
            </div>
            @error('name')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Tanggal Lahir -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}" required>
            </div>
            @error('tanggal_lahir')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Jenis Kelamin -->
            <div class="w-full mt-6">
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                <select id="jenis_kelamin" name="jenis_kelamin" class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500" required>
                    <option value="pria" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'pria' ? 'selected' : '' }}>Pria</option>
                    <option value="wanita" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'wanita' ? 'selected' : '' }}>Wanita</option>
                </select>
            </div>
            @error('jenis_kelamin')
                <div class="text-red-500 mt-2">{{ $message }}</div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-8">
                <!-- Back -->
                <a class="text-lg font-medium text-indigo-500 hover:underline transition-all duration-300" href="{{ route('admin.daftarPengguna') }}">
                    {{ __('Kembali') }}
                </a>

                <!-- Save Changes -->
                <button type="submit" id="editForm" class="transform rounded-sm bg-indigo-500 py-2 px-6 font-bold duration-300 hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <script>
        document.getElementById("editForm").addEventListener("submit", function(event) {
            var button = document.getElementById("editForm");
            button.disabled = true;
        });
    </script>
@endsection