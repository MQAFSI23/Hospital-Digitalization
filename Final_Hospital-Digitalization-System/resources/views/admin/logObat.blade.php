@extends('layouts.app2')

@php
    use Carbon\Carbon;
@endphp

@section('title', 'Log Obat')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Log Obat</h1>

        <!-- Filter Form -->
        <form action="{{ route('admin.logObat') }}" method="GET" class="mt-8">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <!-- Search by Name -->
                <div>
                    <label for="search" class="block text-sm font-medium">Cari Nama Obat</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Masukkan nama obat" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Filter Status -->
                <div>
                    <label for="status" class="block text-sm font-medium">Status</label>
                    <select name="status" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Semua Status</option>
                        <option value="terisi" {{ request('status') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                        <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                    </select>
                </div>

                <!-- Filter Tanggal Dari -->
                <div>
                    <label for="tanggal_awal" class="block text-sm font-medium">Tanggal Dari</label>
                    <input type="date" name="tanggal_awal" 
                        value="{{ request('tanggal_awal') }}"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Filter Tanggal Sampai -->
                <div>
                    <label for="tanggal_akhir" class="block text-sm font-medium">Tanggal Sampai</label>
                    <input type="date" name="tanggal_akhir" 
                        value="{{ request('tanggal_akhir') }}"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded">
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium">Urutkan Berdasarkan</label>
                    <select name="sort_by" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                        <option value="">Pilih Urutan</option>
                        <option value="tanggal_log" {{ request('sort_by') == 'tanggal_log' ? 'selected' : '' }}>Tanggal Log</option>
                        <option value="nama_obat" {{ request('sort_by') == 'nama_obat' ? 'selected' : '' }}>Nama Obat</option>
                    </select>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium">Urutan</label>
                    <select name="sort_order" class="mt-1 block w-full p-2 border border-gray-300 rounded">
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

        <!-- Menampilkan Total Log -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700">
                Total Log: {{ $logObat->count() }}
            </h2>
        </div>

        <!-- Tabel Log Obat -->
        <div class="overflow-hidden bg-white p-6 rounded shadow-lg mt-4">
            <div class="overflow-x-auto max-h-96 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Obat</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Jumlah</th>
                            <th class="py-2 px-4">Tanggal Log</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @foreach ($logObat as $index => $log)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 duration-300">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $log->obat->nama_obat }}</td>
                                <td class="py-2 px-4">{{ ucfirst($log->status) }}</td>
                                <td class="py-2 px-4">{{ $log->jumlah }}</td>
                                <td class="py-2 px-4">{{ Carbon::parse($log->tanggal_log)->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection