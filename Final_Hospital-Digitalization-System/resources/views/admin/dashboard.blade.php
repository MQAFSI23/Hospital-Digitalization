@extends('layouts.app2')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Halo, {{ auth()->user()->name }}👋</p>

        <!-- Menampilkan pesan sukses jika ada -->
        @if (session('success'))
            <div class="p-4 mt-8 text-sm text-green-500 bg-green-100 rounded" role="alert">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="p-4 mt-8 text-sm text-red-500 bg-red-100 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistik -->
        <div class="mt-8 grid lg:grid-cols-3 gap-14 text-center">
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="font-semibold text-xl text-gray-800">Jumlah Pengguna</h2>
                <p class="text-2xl font-bold text-indigo-500">{{ $jumlahPengguna }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="font-semibold text-xl text-gray-800">Jumlah Dokter Hari Ini</h2>
                <p class="text-2xl font-bold text-indigo-500">{{ $jumlahDokterBertugas }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow-lg">
                <h2 class="font-semibold text-xl text-gray-800">Jumlah Pasien Hari Ini</h2>
                <p class="text-2xl font-bold text-indigo-500">{{ $jumlahPasienHariIni }}</p>
            </div>
        </div>

        <!-- Daftar Dokter Bertugas Hari Ini -->
        <div class="max-h-80 overflow-hidden bg-white p-6 rounded shadow-lg mt-8">
            <h2 class="text-xl font-semibold text-gray-800">Dokter yang Bertugas Hari Ini</h2>
            <div class="overflow-y-auto overflow-x-auto max-h-60 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Dokter</th>
                            <th class="py-2 px-4">Jenis Dokter</th>
                            <th class="py-2 px-4">Spesialisasi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($dokterBertugas as $index => $jadwal)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 cursor-pointer"
                                onclick="window.location='{{ route('admin.detailPengguna', $jadwal->dokter->user->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $jadwal->dokter->user->name }}</td> <!-- Nama Dokter -->
                                <td class="py-2 px-4">{{ ucfirst($jadwal->dokter->jenis_dokter) }}</td> <!-- Jenis Dokter -->
                                <td class="py-2 px-4">{{ $jadwal->dokter->spesialisasi == true ? ucfirst($jadwal->dokter->spesialisasi) : '-' }}</td> <!-- Spesialisasi -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-left text-gray-500">Tidak ada dokter yang bertugas hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Pasien Hari Ini -->
        <div class="max-h-80 overflow-hidden bg-white p-6 rounded shadow-lg mt-8">
            <h2 class="text-xl font-semibold text-gray-800">Pasien Hari Ini</h2>
            <div class="overflow-y-auto overflow-x-auto max-h-60 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Pasien</th>
                            <th class="py-2 px-4">Email Pasien</th>
                            <th class="py-2 px-4">Nama Dokter Tujuan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($pasienHariIni as $index => $pasient)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 cursor-pointer"
                                onclick="window.location='{{ route('admin.detailPengguna', $pasient->pasien->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $pasient->pasien->name }}</td>
                                <td class="py-2 px-4">{{ $pasient->pasien->email }}</td>
                                <td class="py-2 px-4">{{ $pasient->dokter->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-left text-gray-500">Tidak ada pasien hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Pengguna Terbaru -->
        <div class="max-h-80 overflow-hidden bg-white p-6 rounded shadow-lg mt-8">
            <h2 class="text-xl font-semibold text-gray-800">Pengguna Baru (Sebulan Terakhir): {{ $jumlahPenggunaTerbaru }}</h2>
            <div class="overflow-y-auto overflow-x-auto max-h-60 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Email</th>
                            <th class="py-2 px-4">Peran</th>
                            <th class="py-2 px-4">Tanggal Registrasi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($penggunaTerbaru as $index => $user)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }} hover:bg-indigo-100 cursor-pointer"
                                onclick="window.location='{{ route('admin.detailPengguna', $user->id) }}';">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                @if ($user->id === auth()->user()->id)
                                    <td class="py-2 px-4">{{ $user->name }} (Anda)</td>
                                @else
                                    <td class="py-2 px-4">{{ $user->name }}</td>
                                @endif
                                <td class="py-2 px-4">{{ $user->email }}</td>
                                <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                                <td class="py-2 px-4">{{ $user->created_at->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-left text-gray-500">Tidak ada pengguna baru dalam sebulan terakhir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection