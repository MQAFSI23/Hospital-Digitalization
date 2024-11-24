@extends('layouts.app2')

@php
    use \Carbon\Carbon;
@endphp

@section('title', 'Dashboard')

@section('content')
<div class="py-10">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="mt-2 text-gray-600">Halo, {{ auth()->user()->name }}👋</p>

    <!-- Informasi Dokter -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Informasi Dokter</h2>
        <div class="mt-4">
            <div class="grid grid-cols-3 gap-2 text-sm md:text-base">
                <p class="font-medium text-gray-600">Nama</p>
                <p class="col-span-2 text-gray-800">: {{ auth()->user()->name }}</p>

                <p class="font-medium text-gray-600">Email</p>
                <p class="col-span-2 text-gray-800">: {{ auth()->user()->email }}</p>

                <p class="font-medium text-gray-600">Jenis Dokter</p>
                <p class="col-span-2 text-gray-800">: {{ ucfirst(auth()->user()->dokter->jenis_dokter) }}</p>

                <p class="font-medium text-gray-600">Spesialisasi</p>
                <p class="col-span-2 text-gray-800">: 
                    {{ auth()->user()->dokter->spesialisasi ? ucfirst(auth()->user()->dokter->spesialisasi) : '-' }}
                </p>

                <!-- Hari Tugas Dokter -->
                <p class="font-medium text-gray-600">Hari Tugas</p>
                <p class="col-span-2 text-gray-800">: 
                    @if($jadwalDokter && $jadwalDokter->count() > 0)
                        {{ implode(', ', $jadwalDokter->pluck('hari_tugas')->toArray()) }}
                    @else
                        Belum ada jadwal tugas untuk hari ini.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Daftar Pasien yang Akan Diperiksa -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pasien yang Akan Diperiksa Hari Ini: {{ $totalKonsul }}</h2>
        <div class="overflow-y-auto overflow-x-auto max-h-60 mt-4">
            <table class="min-w-full border-collapse table-fixed">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-2 px-4 w-16">No</th>
                        <th class="py-2 px-4">Nama Pasien</th>
                        <th class="py-2 px-4">Tindakan</th>
                        <th class="py-2 px-4">Tanggal Konsultasi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($pasienKonsul as $index => $penjadwalan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 text-center cursor-pointer"
                        onclick="window.location='{{ route('dokter.detailPasien', $penjadwalan->pasien->id) }}';">
                        <td class="py-2 px-4">{{ $index + 1 }}</td>
                        <td class="py-2 px-4">{{ $penjadwalan->pasien->name }}</td>

                        <!-- Tampilkan rekam medis jika ada -->
                        @if($penjadwalan->rekamMedis)
                            <td class="py-2 px-4">{{ $penjadwalan->rekamMedis->tindakan ?? 'Belum ada tindakan' }}</td>
                        @else
                            <td class="py-2 px-4">Rekam medis belum ada</td>
                        @endif

                        <td class="py-2 px-4">{{ Carbon::parse($penjadwalan->tanggal_konsultasi)->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-3 text-left text-gray-500">Belum ada pasien yang akan diperiksa hari ini.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pasien yang Telah Diperiksa Hari Ini -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Pasien yang Telah Diperiksa Hari Ini: {{ $totalPasienSelesai }}</h2>
        <div class="overflow-y-auto overflow-x-auto max-h-60 mt-4">
            <table class="min-w-full border-collapse table-fixed">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-2 px-4 w-16">No</th>
                        <th class="py-2 px-4">Nama Pasien</th>
                        <th class="py-2 px-4">Diagnosa</th>
                        <th class="py-2 px-4">Tindakan</th>
                        <th class="py-2 px-4">Tanggal Pemeriksaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasienSelesai as $index => $rekamMedis)
                        <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 text-center cursor-pointer"
                            onclick="window.location='{{ route('dokter.detailPasien', $rekamMedis->pasien->id) }}';">
                            <td class="py-2 px-4">{{ $index + 1 }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->pasien->name }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->diagnosa }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->tindakan }}</td>
                            <td class="py-2 px-4">{{ Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 text-left text-gray-500">Belum ada pasien yang telah diperiksa hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection