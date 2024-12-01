@extends('layouts.app2')

@section('title', 'Detail Notifikasi')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Detail Notifikasi</h1>

        <!-- Informasi Notifikasi -->
        <div class="mt-6 bg-white shadow rounded p-6">
            <h2 class="text-xl font-semibold text-gray-800">{{ $notifikasi->judul }}</h2>
            <p class="mt-4 text-gray-600">
                {{ $notifikasi->deskripsi }} Untuk pasien: <span class="font-semibold">{{ $notifikasi->pasien->user->name }}</span>.
            </p>
            <p class="mt-4 text-sm text-gray-500">Tanggal: {{ Carbon::parse($notifikasi->tanggal)->format('d-m-Y') }}</p>
        </div>

        <!-- Informasi Obat Jika Judul "Pengambilan Obat" -->
        @if (strtolower($notifikasi->judul) === 'pengambilan obat')
            <div class="mt-6 bg-white shadow rounded p-6">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Obat</h3>
                @foreach($notifikasi->pasien->rekamMedisPasien as $rekamMedis)
                    @foreach($rekamMedis->resep as $resep)
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <p class="text-gray-600 font-semibold">Nama Obat</p>
                            <p class="text-gray-600 col-span-2">: {{ $resep->obat->nama_obat }}</p>
                            
                            <p class="text-gray-600 font-semibold">Dosis</p>
                            <p class="text-gray-600 col-span-2">: {{ $resep->dosis }}</p>
                            
                            <p class="text-gray-600 font-semibold">Jumlah</p>
                            <p class="text-gray-600 col-span-2">: {{ $resep->jumlah }}</p>
                            
                            <p class="text-gray-600 font-semibold">Aturan Pakai</p>
                            <p class="text-gray-600 col-span-2">: {{ $resep->aturan_pakai ?? 'Tidak ada' }}</p>
                        </div>
                        <!-- Garis Horizontal Antar Obat -->
                        <hr class="my-4 border-gray-300">
                    @endforeach
                @endforeach
            </div>
        @endif

        <!-- Tombol Kembali -->
        <div class="mt-6">
            <a href="{{ url()->previous() }}"
                class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded hover:bg-indigo-700 duration-300">
                Kembali
            </a>
        </div>
    </div>
@endsection