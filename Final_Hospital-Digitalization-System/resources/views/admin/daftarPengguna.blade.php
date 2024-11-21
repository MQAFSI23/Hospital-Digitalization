@extends('layouts.app2')

@section('title', 'Daftar Pengguna')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pengguna</h1>

        <!-- Filter Form -->
        <form action="{{ route('admin.daftarPengguna') }}" method="GET" class="mt-8">
            @csrf
            <div class="flex flex-col sm:flex-row sm:space-x-4 mb-4">

                <!-- Filter Nama -->
                <div class="mb-4 sm:mb-0 flex-1">
                    <label for="search" class="block text-sm font-medium">Cari Nama Pengguna</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded"
                        placeholder="Masukkan nama pengguna">
                </div>

                <!-- Filter Peran -->
                <div class="mb-4 sm:mb-0">
                    <label for="role" class="block text-sm font-medium">Peran</label>
                    <select name="role" id="role" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dokter" {{ request('role') == 'dokter' ? 'selected' : '' }}>Dokter</option>
                        <option value="pasien" {{ request('role') == 'pasien' ? 'selected' : '' }}>Pasien</option>
                    </select>
                </div>

                <!-- Filter Tanggal Registrasi -->
                <div class="mb-4 sm:mb-0">
                    <label for="date_from" class="block text-sm font-medium">Tanggal Dari</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <div class="mb-4 sm:mb-0">
                    <label for="date_to" class="block text-sm font-medium">Tanggal Sampai</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

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
                        @foreach ($daftarPengguna as $index => $user)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} cursor-pointer hover:bg-indigo-100 duration-300"
                                onclick="window.location='{{ route('admin.detailPengguna', $user->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $user->name }}</td>
                                <td class="py-2 px-4">{{ $user->email }}</td>
                                <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                                <td class="py-2 px-4">
                                    {{ $user->created_at ? Carbon::parse($user->created_at)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-2 px-4" onclick="event.stopPropagation();">
                                    <a href="{{ route('admin.editPengguna', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded mr-2 duration-300">
                                        Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.hapusPengguna', $user->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} ({{ $user->email }})?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded duration-300">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection