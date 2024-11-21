@extends('layouts.app2')

@section('title', 'Daftar Obat')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Obat</h1>

        <div class="mt-8">
            <a href="{{ route('admin.registerObat') }}" class="bg-indigo-500 duration-300 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Register Obat Baru
            </a>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.daftarObat') }}" method="GET" class="mt-8">
            @csrf
            <div class="flex flex-col sm:flex-row sm:space-x-4 mb-4">

                <!-- Search by Name -->
                <div class="mb-4 sm:mb-0">
                    <label for="search" class="block text-sm font-medium">Cari Nama Obat</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Masukkan nama obat" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Filter Status Ketersediaan Obat -->
                <div class="mb-4 sm:mb-0">
                    <label for="status_ketersediaan" class="block text-sm font-medium">Status Ketersediaan</label>
                    <select name="status_ketersediaan" id="status_ketersediaan" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status_ketersediaan') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="tidak tersedia" {{ request('status_ketersediaan') == 'tidak tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        <option value="kedaluwarsa" {{ request('status_ketersediaan') == 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>

                <button type="submit" class="bg-indigo-500 duration-300 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>

        <!-- Menampilkan Total Obat -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700">
                Total Obat: {{ $daftarObat->count() }}
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

        <!-- Tabel Daftar Obat -->
        <div class="overflow-hidden bg-white p-6 rounded shadow-lg mt-2">
            <div class="overflow-x-auto max-h-96 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Obat</th>
                            <th class="py-2 px-4">Tipe</th>
                            <th class="py-2 px-4">Stok</th>
                            <th class="py-2 px-4">Tanggal Kedaluwarsa</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @foreach ($daftarObat as $index => $obat)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} cursor-pointer hover:bg-indigo-100 duration-300" onclick="window.location='{{ route('admin.detailObat', $obat->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $obat->nama_obat }}</td>
                                <td class="py-2 px-4">{{ ucfirst($obat->tipe_obat) }}</td>
                                <td class="py-2 px-4">{{ $obat->stok }}</td>
                                <td class="py-2 px-4">
                                    {{ $obat->kedaluwarsa ? Carbon::parse($obat->kedaluwarsa)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-2 px-4" onclick="event.stopPropagation();">
                                    <a href="{{ route('admin.editObat', $obat->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded mr-2 duration-300">
                                        Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.hapusObat', $obat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat {{ $obat->nama_obat }}?');">
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