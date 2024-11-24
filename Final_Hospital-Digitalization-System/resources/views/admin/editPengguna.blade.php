@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium mt-4">Edit User</div>

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
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Name -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="name" :value="__('Nama')" />
                <input type="text" id="name" name="name" placeholder="Name"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="off">
            </div>
            @error('name')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tanggal Lahir -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}" required>
            </div>
            @error('tanggal_lahir')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
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
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Role -->
            <div class="mt-6">
                <x-input-label for="role" :value="__('Role')" />
                <select id="role" name="role" class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dokter" {{ old('role', $user->role) === 'dokter' ? 'selected' : '' }}>Dokter</option>
                    <option value="pasien" {{ old('role', $user->role) === 'pasien' ? 'selected' : '' }}>Pasien</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('role')" class="text-red-600 text-sm mt-2" />

            <!-- Additional Inputs for Dokter -->
            <div id="dokterDetails" class="hidden mt-6 text-lg">
                <!-- Jenis Dokter -->
                <div class="mt-6">
                    <x-input-label for="jenis_dokter" :value="__('Jenis Dokter')" />
                    <select id="jenis_dokter" name="jenis_dokter" class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500" required>
                        @foreach ($jenisDokterOptions as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_dokter', $user->dokter->jenis_dokter ?? '') === $jenis ? 'selected' : '' }}>
                                {{ ucfirst($jenis) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('jenis_dokter')" class="text-red-600 text-sm mt-2" />

                <!-- Spesialisasi -->
                <div id="spesialisasiField" class="hidden mt-6">
                    <x-input-label for="spesialisasi" :value="__('Spesialisasi')" />
                    <select id="spesialisasi" name="spesialisasi" class="relative w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                        @foreach ($spesialisasiOptions as $spesialisasi)
                            <option value="{{ $spesialisasi }}" {{ old('spesialisasi', $user->dokter->spesialisasi ?? '') === $spesialisasi ? 'selected' : '' }}>
                                {{ ucfirst($spesialisasi) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('spesialisasi')" class="text-red-600 text-sm mt-2" />

                <!-- Jadwal Tugas -->
                <div id="jadwalTugas" class="hidden mt-6">
                    <x-input-label for="jadwal_tugas" :value="__('Hari Tugas')" />
                    <div>
                        @foreach ($hariOptions as $hari)
                            <label class="block">
                                <input type="checkbox" name="jadwal_tugas[]" value="{{ $hari }}"
                                    class="mr-2"
                                    {{ in_array($hari, $selectedHari) ? 'checked' : '' }}>
                                {{ $hari }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('jadwal_tugas')" class="text-red-600 text-sm mt-2" />
                </div>
            </div>

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
            <div class="flex items-center justify-between mt-8 mb-4">
                <!-- Back -->
                <a class="text-lg font-medium text-indigo-500 hover:underline transition-all duration-300" href="{{ route('admin.daftarPengguna') }}">
                    {{ __('Kembali') }}
                </a>

                <!-- Save Changes -->
                <button type="submit" id="editForm" class="transform rounded-sm bg-indigo-500 py-2 px-2 font-bold duration-300 hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <script>
        const roleInput = document.getElementById('role');
        const jadwalTugas = document.getElementById('jadwalTugas');
        const dokterDetails = document.getElementById('dokterDetails');
        const jenisDokter = document.getElementById('jenis_dokter');
        const spesialisasiField = document.getElementById('spesialisasiField');

        function updateVisibility() {
            if (roleInput.value === 'dokter') {
                dokterDetails.classList.remove('hidden');
                jadwalTugas.classList.remove('hidden');
                if (jenisDokter.value === 'spesialis') {
                    spesialisasiField.classList.remove('hidden');
                } else {
                    spesialisasiField.classList.add('hidden');
                }
            } else {
                dokterDetails.classList.add('hidden');
                spesialisasiField.classList.add('hidden');
            }
        }

        roleInput.addEventListener('change', updateVisibility);

        jenisDokter.addEventListener('change', function () {
            spesialisasiField.classList.toggle('hidden', jenisDokter.value !== 'spesialis');
        });

        document.addEventListener('DOMContentLoaded', updateVisibility);

        document.getElementById("editForm").addEventListener("submit", function(event) {
            var button = document.getElementById("editForm");
            button.disabled = true;
        });
    </script>
@endsection