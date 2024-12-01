@extends('layouts.app2')

@section('title', 'Daftar Pengguna')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pengguna</h1>

        <!-- Register User Baru -->
        <div class="mt-8 flex justify-center sm:justify-start">
            <a href="{{ route('register-admin') }}" 
                class="w-full sm:w-auto max-w-xs sm:max-w-none bg-indigo-500 duration-300 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                Register Pengguna Baru
            </a>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.daftarPengguna') }}" method="GET" class="mt-8">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

                <!-- Filter Nama -->
                <div>
                    <label for="search" class="block text-sm font-medium">Cari Nama Pengguna</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Masukkan nama pengguna"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Filter Peran -->
                <div>
                    <label for="role" class="block text-sm font-medium">Peran</label>
                    <select name="role" id="role" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dokter" {{ request('role') == 'dokter' ? 'selected' : '' }}>Dokter</option>
                        <option value="pasien" {{ request('role') == 'pasien' ? 'selected' : '' }}>Pasien</option>
                    </select>
                </div>

                <!-- Filter Tanggal Dari -->
                <div>
                    <label for="date_from" class="block text-sm font-medium">Tanggal Dari</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Filter Tanggal Sampai -->
                <div>
                    <label for="date_to" class="block text-sm font-medium">Tanggal Sampai</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium">Urutkan Berdasarkan</label>
                    <select name="sort_by" id="sort_by" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Pilih Urutan</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Pengguna</option>
                        <option value="role" {{ request('sort_by') == 'role' ? 'selected' : '' }}>Peran</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Registrasi</option>
                    </select>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium">Arah Urutan</label>
                    <select name="sort_order" id="sort_order" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Menaik</option>
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Menurun</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-500 duration-300 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>

        <!-- Menampilkan Total Pengguna -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700">
                Total Pengguna: {{ $daftarPengguna->count() }}
            </h2>
        </div>

        <div class="mt-4">
            @if (session('status'))
                <div class="p-4 mb-4 text-sm text-green-500 bg-green-100 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @elseif (session('nothing'))
                <div class="p-4 mb-4 text-sm text-blue-500 bg-blue-100 rounded" role="alert">
                    {{ session('nothing') }}
                </div>
            @elseif (session('error'))
                <div class="p-4 mb-4 text-sm text-red-500 bg-red-100 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Tabel Daftar Pengguna -->
        <div class="overflow-hidden bg-white p-6 rounded shadow-lg mt-2">
            <div class="overflow-x-auto max-h-96 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Email</th>
                            <th class="py-2 px-4">Peran</th>
                            <th class="py-2 px-4">Tanggal Registrasi</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($daftarPengguna as $index => $user)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} cursor-pointer hover:bg-indigo-100 duration-300"
                                onclick="window.location='{{ route('admin.detailPengguna', $user->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                @if ($user->id === auth()->user()->id)
                                    <td class="py-2 px-4">{{ $user->name }} (Anda)</td>
                                @else
                                    <td class="py-2 px-4">{{ $user->name }}</td>
                                @endif
                                <td class="py-2 px-4">{{ $user->email }}</td>
                                <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                                <td class="py-2 px-4">
                                    {{ $user->created_at ? Carbon::parse($user->created_at)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-2 px-4" onclick="event.stopPropagation();">
                                    <a href="{{ route('admin.editPengguna', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded mr-2 duration-300">
                                        Edit
                                    </a>
                                    
                                    <x-danger-button
                                        class="!bg-red-500 hover:!bg-red-700 !rounded !duration-300"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')"
                                    >{{ __('Hapus') }}</x-danger-button>

                                    <x-modal name="confirm-user-deletion-{{ $user->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                        <form method="post" action="{{ route('admin.hapusPengguna', $user->id) }}" class="p-6">
                                            @csrf
                                            @method('DELETE')

                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Apakah Anda yakin ingin menghapus akun ini?') }}
                                            </h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ __('Setelah akun ini dihapus, semua data dan informasi terkait akan hilang secara permanen. Masukkan password Anda untuk mengonfirmasi penghapusan.') }}
                                            </p>

                                            <div class="mt-6">
                                                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                                                <x-text-input
                                                    id="password"
                                                    name="password"
                                                    type="password"
                                                    class="transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 w-3/4"
                                                    placeholder="{{ __('Password Anda') }}"
                                                    required
                                                />

                                                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                            </div>

                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')" class="duration-300">
                                                    {{ __('Batal') }}
                                                </x-secondary-button>

                                                <x-danger-button type="submit" class="ms-3 !bg-red-500 hover:!bg-red-700 !rounded !duration-300">
                                                    {{ __('Hapus Akun') }}
                                                </x-danger-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-left text-gray-500">Tidak ada pengguna yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection