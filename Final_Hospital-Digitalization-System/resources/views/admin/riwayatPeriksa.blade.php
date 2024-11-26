@extends('layouts.app2')

@section('title', 'Daftar Pasien')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pasien yang Telah Diperiksa</h1>

        <!-- Filter Form -->
        <form action="{{ route('admin.riwayatPeriksa') }}" method="GET" class="mt-8">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

                <!-- Filter Nama Pasien atau Dokter -->
                <div>
                    <label for="search" class="block text-sm font-medium">Cari Nama</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Masukkan nama pasien atau dokter"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
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
                        <option value="dokter.name" {{ request('sort_by') == 'dokter.name' ? 'selected' : '' }}>Nama Dokter</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Pasien</option>
                        <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Tanggal Pemeriksaan</option>
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
                <button type="submit" class="bg-indigo-500 duration-300 hover:bg-indigo-700 text-center text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>

        <!-- Menampilkan Total Pasien -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700">
                Total Pasien: {{ $daftarPasien->count() }}
            </h2>
        </div>

        <!-- Tabel Daftar Pasien -->
        <div class="overflow-hidden bg-white p-6 rounded shadow-lg mt-2">
            <div class="overflow-x-auto max-h-96 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Pasien</th>
                            <th class="py-2 px-4">Nama Dokter</th>
                            <th class="py-2 px-4">Tanggal Pemeriksaan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($daftarPasien as $index => $rekamMedis)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 duration-300">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $rekamMedis->pasien->user->name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $rekamMedis->dokter->user->name ?? '-' }}</td>
                                <td class="py-2 px-4">
                                    {{ $rekamMedis->tanggal_berobat ? Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 text-left text-gray-500">Tidak ada pasien yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection