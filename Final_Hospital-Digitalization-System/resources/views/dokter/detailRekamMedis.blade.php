@extends('layouts.app2')

@php
    use \Carbon\Carbon;
@endphp

@section('title', 'Detail Riwayat Konsultasi')

@section('content')
<div class="py-10">
    <h1 class="text-3xl font-bold text-gray-800">Detail Riwayat Konsultasi</h1>

    <!-- Detail Riwayat -->
    <div class="mt-8 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Informasi Konsultasi</h2>
        <div class="mt-4">
            <div class="grid grid-cols-3 gap-2 text-sm md:text-base">
                <p class="font-medium text-gray-600">Pasien</p>
                <p class="col-span-2 text-gray-800">: {{ $rekamMedis->pasien->name }}</p>

                <p class="font-medium text-gray-600">Tindakan</p>
                <p class="col-span-2 text-gray-800">: {{ $rekamMedis->tindakan }}</p>

                <p class="font-medium text-gray-600">Diagnosa</p>
                <p class="col-span-2 text-gray-800">: {{ $rekamMedis->diagnosa }}</p>

                <p class="font-medium text-gray-600">Tanggal Konsultasi</p>
                <p class="col-span-2 text-gray-800">: {{ Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Informasi Obat yang Diberikan -->
    <div class="mt-6 bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">Obat yang Diberikan</h2>
        <div class="mt-4">
            @if ($rekamMedis->obats->isNotEmpty())
                <table class="w-full border border-gray-300 rounded text-sm md:text-base">
                    <thead>
                        <tr class="bg-indigo-600">
                            <th class="border px-4 py-2 text-left text-white">Nama Obat</th>
                            <th class="border px-4 py-2 text-left text-white">Tipe Obat</th>
                            <th class="border px-4 py-2 text-left text-white">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekamMedis->obats as $obat)
                            <tr>
                                <td class="border px-4 py-2 text-gray-800">{{ $obat->nama_obat }}</td>
                                <td class="border px-4 py-2 text-gray-800 capitalize">{{ $obat->tipe_obat }}</td>
                                <td class="border px-4 py-2 text-gray-800">{{ $obat->deskripsi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">Tidak ada obat yang diberikan untuk konsultasi ini.</p>
            @endif
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="mt-6">
        <a href="{{ route('dokter.detailPasien', $rekamMedis->pasien->id) }}" 
            class="bg-indigo-500 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            Kembali
        </a>
    </div>
</div>
@endsection