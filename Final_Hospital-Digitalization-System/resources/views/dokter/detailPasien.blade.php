@extends('layouts.app2')

@php
    use \Carbon\Carbon;
@endphp

@section('title', 'Detail Pasien')

@section('content')
<div class="py-10">
    <h1 class="text-3xl font-bold text-gray-800">Detail Pasien</h1>

    <!-- Informasi Pasien -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Informasi Pasien</h2>
        <div class="mt-4">
            <div class="grid grid-cols-3 gap-2 text-sm md:text-base">
                <p class="font-medium text-gray-600">Nama</p>
                <p class="col-span-2 text-gray-800">: {{ $pasien->name }}</p>

                <p class="font-medium text-gray-600">Tanggal Lahir</p>
                <p class="col-span-2 text-gray-800">: {{ Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') }}</p>

                <p class="font-medium text-gray-600">Umur</p>
                <p class="col-span-2 text-gray-800">: 
                    {{ Carbon::parse($pasien->tanggal_lahir)->age }} tahun
                </p>

                <p class="font-medium text-gray-600">Jenis Kelamin</p>
                <p class="col-span-2 text-gray-800">: {{ ucfirst($pasien->jenis_kelamin) }}</p>
            </div>
        </div>
    </div>

    <!-- Riwayat Konsultasi -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Riwayat Konsultasi</h2>
        <div class="overflow-y-auto overflow-x-auto max-h-60 mt-4">
            <table class="min-w-full border-collapse table-fixed">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-2 px-4 w-16">No</th>
                        <th class="py-2 px-4">Tanggal</th>
                        <th class="py-2 px-4">Tindakan</th>
                        <th class="py-2 px-4">Diagnosa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatKonsultasi as $index => $rekamMedis)
                        <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 text-center cursor-pointer"
                            onclick="window.location='{{ route('dokter.detailRekamMedis', $rekamMedis->id) }}';">
                            <td class="py-2 px-4">{{ $index + 1 }}</td>
                            <td class="py-2 px-4">{{ Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->tindakan }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->diagnosa }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 text-left text-gray-500">Tidak ada riwayat konsultasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('dokter.daftarPasien') }}" 
            class="bg-indigo-500 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            Kembali
        </a>
    </div>

</div>
@endsection