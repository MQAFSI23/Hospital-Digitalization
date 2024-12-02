@extends('layouts.app2')

@section('title', 'Semua Notifikasi')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Semua Notifikasi</h1>

        <!-- Notifikasi -->
        <div class="mt-8">
            <div class="mt-4 bg-white shadow rounded">
                @forelse($notifikasi as $notif)
                    <ul class="divide-y divide-gray-200">
                        <li class="p-4 hover:bg-gray-200 {{ $notif->status === 1 ? 'bg-gray-100' : '' }}">
                            <a href="{{ route('pasien.detailNotifikasi', $notif->id) }}" class="block">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-base sm:text-lg font-semibold text-indigo-700">
                                            {{ $notif->judul }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notif->deskripsi }} 
                                            | Untuk pasien: <span class="font-semibold">{{ $notif->pasien->user->name }}</span>
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2 sm:mt-0">
                                        {{ Carbon::parse($notif->tanggal)->format('d-m-Y') }}
                                    </p>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <hr class="border-indigo-300">
                @empty
                    <div class="p-4 text-gray-500 text-center">
                        Tidak ada notifikasi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection