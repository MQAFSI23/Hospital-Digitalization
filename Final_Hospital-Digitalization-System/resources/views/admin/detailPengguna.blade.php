@extends('layouts.app2')

@section('title', 'Detail Pengguna')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white shadow-lg rounded p-10 w-full max-w-md md:max-w-lg lg:max-w-xl">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Detail Pengguna</h2>

            <!-- Informasi Pengguna -->
            <div class="space-y-4 md:space-y-6">
                @foreach ([
                    'Nama' => $user->name,
                    'Username' => $user->username,
                    'Email' => $user->email,
                    'Peran' => ucfirst($user->role),
                    'Tanggal Lahir' => Carbon::parse($user->tanggal_lahir)->format('d-m-Y') ?? 'Belum diisi',
                    'Jenis Kelamin' => ucfirst($user->jenis_kelamin) ?? 'Belum diisi'
                ] as $label => $value)
                    <div class="flex flex-wrap justify-between items-center border-b border-gray-200 py-2">
                        <h3 class="font-semibold text-gray-600 text-sm md:text-base w-full md:w-1/3">{{ $label }}</h3>
                        <p class="text-gray-800 text-base md:text-lg w-full md:w-2/3 text-left md:text-right">{{ $value }}</p>
                    </div>

                    <!-- Tambahkan Jenis Dokter dan Spesialisasi setelah Peran -->
                    @if ($label === 'Peran' && $user->role === 'dokter')
                        <div class="flex flex-wrap justify-between items-center border-b border-gray-200 py-2">
                            <h3 class="font-semibold text-gray-600 text-sm md:text-base w-full md:w-1/3">Jenis Dokter</h3>
                            <p class="text-gray-800 text-base md:text-lg w-full md:w-2/3 text-left md:text-right">{{ ucfirst($dokter->jenis_dokter) ?? 'Belum diisi' }}</p>
                        </div>
                        @if ($dokter->jenis_dokter === 'spesialis')
                            <div class="flex flex-wrap justify-between items-center border-b border-gray-200 py-2">
                                <h3 class="font-semibold text-gray-600 text-sm md:text-base w-full md:w-1/3">Spesialisasi</h3>
                                <p class="text-gray-800 text-base md:text-lg w-full md:w-2/3 text-left md:text-right">{{ ucfirst($dokter->spesialisasi) ?? 'Belum diisi' }}</p>
                            </div>
                        @endif
                        <!-- Tambahkan Jadwal Tugas -->
                        <div class="flex flex-wrap justify-between items-center border-b border-gray-200 py-2">
                            <h3 class="font-semibold text-gray-600 text-sm md:text-base w-full md:w-1/3">Jadwal Tugas</h3>
                            @if ($dokter->jadwalTugas->isNotEmpty())
                                <ul class="list-disc list-inside text-gray-800 text-base md:text-lg">
                                    @foreach ($dokter->jadwalTugas as $jadwal)
                                        <li>{{ $jadwal->hari_tugas }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-800 text-base md:text-lg w-full md:w-2/3 text-left md:text-right">Belum diisi</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-6 space-y-4 md:space-y-0">
                <a href="{{ route('admin.daftarPengguna') }}" 
                    class="bg-indigo-500 hover:bg-indigo-700 duration-300 text-white text-sm md:text-base font-medium py-2 px-4 md:px-6 rounded w-full md:w-auto text-center">
                    Kembali
                </a>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                    <a href="{{ route('admin.editPengguna', $user->id) }}" 
                        class="bg-yellow-500 hover:bg-yellow-700 duration-300 text-white text-sm md:text-base font-medium py-2 px-4 md:px-6 rounded text-center">
                        Edit
                    </a>
                    <form action="{{ route('admin.hapusPengguna', $user->id) }}" method="POST" 
                            class="w-full md:w-auto"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 duration-300 text-white text-sm md:text-base font-medium py-2 px-4 md:px-6 rounded w-full md:w-auto">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection