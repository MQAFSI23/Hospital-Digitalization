@extends('layouts.app2')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Halo, {{ auth()->user()->name }}. Selamat datang di halaman Admin Dashboard!</p>

        <!-- Menampilkan pesan sukses jika ada -->
        @if (session('success'))
            <div class="p-4 mt-8 text-sm text-green-500 bg-green-100 rounded" role="alert">
                {{ session('success') }}
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
            <h2 class="text-xl font-semibold text-gray-800">Dokter Bertugas Hari Ini</h2>
            <div class="overflow-y-auto overflow-x-auto max-h-60 mt-2">
                <table class="min-w-full border-collapse table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-4 w-16">No</th>
                            <th class="py-2 px-4">Nama Dokter</th>
                            <th class="py-2 px-4">Jenis Dokter</th>
                            <th class="py-2 px-4">Spesialisasi</th>
                            <th class="py-2 px-4">Jam Tugas</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($dokterBertugas as $index => $jadwal)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }}">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $jadwal->dokter->user->name }}</td> <!-- Nama Dokter -->
                                <td class="py-2 px-4">{{ ucfirst($jadwal->dokter->jenis_dokter) }}</td> <!-- Jenis Dokter -->
                                <td class="py-2 px-4">{{ $jadwal->dokter->spesialisasi ?? '-' }}</td> <!-- Spesialisasi -->
                                <td class="py-2 px-4">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td> <!-- Jam Tugas -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Register User Baru -->
        <div class="mt-8">
            <a href="{{ route('register-admin') }}" class="bg-indigo-500 duration-300 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Register User Baru
            </a>
        </div>

        <!-- Tabel Pengguna Terbaru -->
        <div class="max-h-80 overflow-hidden bg-white p-6 rounded shadow-lg mt-8">
            <h2 class="text-xl font-semibold text-gray-800">Pengguna Terbaru (Sebulan Terakhir)</h2>
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
                        @foreach ($penggunaTerbaru as $index => $user)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : 'bg-white' }}">
                                <td class="py-2 px-4">{{ $index + 1 }}</td>
                                <td class="py-2 px-4">{{ $user->name }}</td>
                                <td class="py-2 px-4">{{ $user->email }}</td>
                                <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                                <td class="py-2 px-4">{{ $user->created_at->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection