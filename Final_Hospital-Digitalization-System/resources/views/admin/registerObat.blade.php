@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Register Obat</div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-500">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Register Obat -->
        <form method="POST" action="{{ route('admin.registerObat.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Nama Obat -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="nama_obat" :value="__('Nama Obat')" />
                <input type="text" id="nama_obat" name="nama_obat" placeholder="Nama Obat"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('nama_obat') }}" required>
            </div>
            @error('nama_obat')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Deskripsi -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" rows="4" required>{{ old('deskripsi') }}</textarea>
            </div>
            @error('deskripsi')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tipe Obat -->
            <div class="w-full mt-6">
                <x-input-label for="tipe_obat" :value="__('Tipe Obat')" />
                <select id="tipe_obat" name="tipe_obat" class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500" required>
                    <option value="keras" {{ old('tipe_obat') == 'keras' ? 'selected' : '' }}>Keras</option>
                    <option value="biasa" {{ old('tipe_obat') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                </select>
            </div>
            @error('tipe_obat')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Stok -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="stok" :value="__('Stok')" />
                <input type="number" id="stok" name="stok" placeholder="Stok"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('stok') }}" required>
            </div>
            @error('stok')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Gambar Obat -->
            <div class="w-full mt-6">
                <x-input-label for="gambar_obat" :value="__('Gambar Obat')" />
                <input type="file" id="gambar_obat" name="gambar_obat" class="w-full bg-transparent border-b-2" />
            </div>
            @error('gambar_obat')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tanggal Kedaluwarsa -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="kedaluwarsa" :value="__('Tanggal Kedaluwarsa')" />
                <input type="date" id="kedaluwarsa" name="kedaluwarsa" placeholder="Tanggal Kedaluwarsa"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('kedaluwarsa') }}" required>
            </div>
            @error('kedaluwarsa')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Password Admin untuk Konfirmasi -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="admin_password" :value="__('Password Admin')" />
                <input type="password" id="admin_password" name="admin_password" placeholder="Masukkan Password Admin"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
            </div>
            @error('admin_password')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-8">
                <!-- Back -->
                <a class="text-lg font-medium text-indigo-500 hover:underline transition-all duration-300" href="{{ route('admin.daftarObat') }}">
                    {{ __('Kembali') }}
                </a>

                <!-- Register Button -->
                <button type="submit" id="registerForm" class="transform rounded-sm bg-indigo-500 py-2 px-2 font-bold duration-300 hover:bg-indigo-700">
                    Register Obat
                </button>
            </div>
        </form>

    </section>
@endsection