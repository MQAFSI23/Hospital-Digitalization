@extends('layouts.app2')

@php
    use \Carbon\Carbon;
@endphp

@section('title', 'Detail Rekam Medis')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Detail Rekam Medis</h1>

        <!-- Detail Riwayat -->
        <div class="mt-8 bg-white p-6 rounded shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800">Informasi Konsultasi</h2>
            <div class="mt-4">
                <div class="grid grid-cols-3 gap-2 text-sm md:text-base">
                    <p class="font-medium text-gray-600">Pasien</p>
                    <p class="col-span-2 text-gray-800">: {{ $rekamMedis->pasien->user->name ?? '-' }}</p>

                    <p class="font-medium text-gray-600">Dokter</p>
                    <p class="col-span-2 text-gray-800">: {{ $rekamMedis->dokter->user->name ?? '-' }}</p>

                    <p class="font-medium text-gray-600">Tindakan</p>
                    <p class="col-span-2 text-gray-800">: {{ $rekamMedis->tindakan }}</p>

                    <p class="font-medium text-gray-600">Diagnosa</p>
                    <p class="col-span-2 text-gray-800">: {{ $rekamMedis->diagnosa }}</p>

                    <p class="font-medium text-gray-600">Tanggal Konsultasi</p>
                    <p class="col-span-2 text-gray-800">: {{ Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') }}</p>

                    <p class="font-medium text-gray-600">Status Pengambilan Obat</p>
                    @if ($rekamMedis->resep->isNotEmpty())
                        <p class="col-span-2 text-gray-800">
                            : {{ $rekamMedis->resep->every('status_pengambilan') ? 'Selesai' : 'Belum Selesai' }}
                        </p>
                    @else
                        <p class="col-span-2 text-gray-800">: Tidak perlu obat</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Obat yang Diberikan -->
        <div class="mt-6 bg-white p-6 rounded shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800">Obat yang Diberikan</h2>
            <div class="mt-4">
                @if ($rekamMedis->resep->isNotEmpty())
                    <table class="w-full border border-gray-300 rounded text-sm md:text-base text-center">
                        <thead>
                            <tr class="bg-indigo-600">
                                <th class="border px-4 py-2 text-white">Nama Obat</th>
                                <th class="border px-4 py-2 text-white">Tipe Obat</th>
                                <th class="border px-4 py-2 text-white">Jumlah</th>
                                <th class="border px-4 py-2 text-white">Dosis</th>
                                <th class="border px-4 py-2 text-white">Aturan Pakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekamMedis->resep as $resep)
                                <tr>
                                    <td class="border px-4 py-2 text-gray-800">{{ $resep->obat->nama_obat ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-gray-800 capitalize">{{ $resep->obat->tipe_obat ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-gray-800">{{ $resep->jumlah ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-gray-800">{{ $resep->dosis ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-gray-800">{{ $resep->aturan_pakai ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-600">Tidak ada obat yang diberikan untuk konsultasi ini.</p>
                @endif
            </div>
        </div>

        <!-- Tombol Kembali dan Validasi -->
        <div class="mt-6 flex justify-between">
            <a href="{{ route('admin.riwayatPeriksa') }}" 
                class="bg-indigo-500 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                Kembali
            </a>

            @if (!$rekamMedis->resep->every('status_pengambilan'))
                <button onclick="document.getElementById('modal-selesaikan-{{ $rekamMedis->id }}').classList.remove('hidden')" 
                    class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Tandai Selesai
                </button>
            @endif
        </div>
    </div>

    <!-- Modal Validasi -->
    @if (!$rekamMedis->resep->every('status_pengambilan'))
    <div id="modal-selesaikan-{{ $rekamMedis->id }}" tabindex="-1" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Konfirmasi Penyelesaian</h2>
            <p class="text-gray-700">Apakah Anda yakin ingin menandai status pengambilan obat ini sebagai selesai?</p>
            <div class="mt-6 flex justify-end gap-2">
                <form action="{{ route('admin.updateResepStatus', $rekamMedis->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                        class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Selesai
                    </button>
                </form>
                <button onclick="document.getElementById('modal-selesaikan-{{ $rekamMedis->id }}').classList.add('hidden')" 
                    class="bg-indigo-500 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    Batal
                </button>
            </div>
        </div>
    </div>
    @endif
@endsection