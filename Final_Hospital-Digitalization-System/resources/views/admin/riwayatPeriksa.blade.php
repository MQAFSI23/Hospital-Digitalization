@extends('layouts.app2')

@section('title', 'Rekam Medis')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Rekam Medis</h1>

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

        <div class="mt-4">
            @if (session('status'))
                <div class="p-4 mb-4 text-sm text-green-500 bg-green-100 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>

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
                            <th class="py-2 px-4">Status Pengambilan Obat</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($daftarPasien as $index => $rekamMedis)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 duration-300 cursor-pointer"
                                onclick="window.location='{{ route('admin.detailRiwayatPeriksa', $rekamMedis->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $rekamMedis->pasien->user->name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $rekamMedis->dokter->user->name ?? '-' }}</td>
                                <td class="py-2 px-4">
                                    {{ $rekamMedis->tanggal_berobat ? Carbon::parse($rekamMedis->tanggal_berobat)->format('d-m-Y') : '-' }}
                                </td>
                                @if ($rekamMedis->resep->isEmpty())
                                    <td class="py-2 px-4 text-gray-500">Tidak Perlu Obat</td>
                                    <td class="py-2 px-4 text-gray-500">-</td>
                                @else
                                    <td class="py-2 px-4">
                                        @php
                                            $statusPengambilan = $rekamMedis->resep->every('status_pengambilan');
                                        @endphp
                                        <span class="{{ $statusPengambilan ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $statusPengambilan ? 'Selesai' : 'Belum Diambil' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4" onclick="event.stopPropagation();">
                                        @if (!$statusPengambilan)
                                            <button data-modal-target="modal-{{ $rekamMedis->id }}" 
                                                class="bg-indigo-500 hover:bg-indigo-700 text-white py-1 px-4 rounded duration-300">
                                                Selesaikan
                                            </button>
                                        @else
                                            <span class="text-gray-500">Selesai</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            <!-- Modal -->
                            <div id="modal-{{ $rekamMedis->id }}" tabindex="-1"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg p-6">
                                    <h2 class="text-xl font-semibold mb-4">Konfirmasi</h2>
                                    <p>Apakah Anda yakin ingin menandai status pengambilan obat ini sebagai selesai?</p>
                                    <div class="flex justify-end mt-6">
                                        <form action="{{ route('admin.updateResepStatus', $rekamMedis->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                class="bg-green-500 hover:bg-green-700 text-white py-1 px-4 rounded">
                                                Ya
                                            </button>
                                        </form>
                                        <button onclick="document.getElementById('modal-{{ $rekamMedis->id }}').classList.add('hidden')" 
                                            class="ml-2 bg-indigo-500 hover:bg-indigo-700 text-white py-1 px-4 rounded">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-start py-3 text-gray-500">Tidak ada pasien yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                document.getElementById(modalId).classList.remove('hidden');
            });
        });
    </script>
@endsection