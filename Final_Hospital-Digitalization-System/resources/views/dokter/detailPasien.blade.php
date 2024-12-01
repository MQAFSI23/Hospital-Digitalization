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
                <p class="col-span-2 text-gray-800">: {{ $pasien->user->name }}</p>

                <p class="font-medium text-gray-600">Tanggal Lahir</p>
                <p class="col-span-2 text-gray-800">:
                    {{ Carbon::parse($pasien->user->tanggal_lahir)->format('d-m-Y') }}
                    ({{ Carbon::parse($pasien->user->tanggal_lahir)->age }} tahun)
                </p>

                <p class="font-medium text-gray-600">Jenis Kelamin</p>
                <p class="col-span-2 text-gray-800">: {{ ucfirst($pasien->user->jenis_kelamin) }}</p>
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
                        <th class="py-2 px-4">Tindakan</th>
                        <th class="py-2 px-4">Diagnosa</th>
                        <th class="py-2 px-4">Tanggal</th>
                        <th class="py-2 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatKonsultasi as $index => $rekamMedis)
                        <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 text-center cursor-pointer"
                            onclick="window.location='{{ route('dokter.detailRekamMedis', $rekamMedis->id) }}';">
                            <td class="py-2 px-4">{{ $index + 1 }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->tindakan }}</td>
                            <td class="py-2 px-4">{{ $rekamMedis->diagnosa }}</td>
                            <td class="py-2 px-4">{{ Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') }}</td>
                            <td onclick="event.stopPropagation();">
                                <a onclick="openDeleteModal({{ $rekamMedis->id }})" 
                                        class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded duration-300">
                                    Hapus
                                </a>
                            </td>
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

    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus rekam medis ini?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-4">
                    <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 duration-300">
                        Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                            class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-700 duration-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('dokter.daftarPasien') }}" 
            class="bg-indigo-500 hover:bg-indigo-700 text-white px-4 py-2 rounded duration-300">
            Kembali
        </a>
    </div>

</div>

<script>
    function openDeleteModal(rekamMedisId) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = `/rekam-medis/${rekamMedisId}`;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
@endsection