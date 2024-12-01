@extends('layouts.app2')

@section('title', 'Dashboard')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="mt-4 text-gray-600">Halo, {{ auth()->user()->name }}👋</p>

        <!-- Notifikasi -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800">Notifikasi</h2>
            <div class="mt-4 bg-white shadow rounded-lg">
                @if($notifikasi->isNotEmpty())
                    <ul class="divide-y divide-gray-200">
                        @foreach($notifikasi as $notif)
                            <li class="p-4 hover:bg-gray-100">
                                <a href="{{ route('pasien.detailNotifikasi', $notif->id) }}" class="block">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-medium text-gray-800">
                                                {{ $notif->judul }}
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $notif->deskripsi }} 
                                                Untuk pasien: <span class="font-semibold">{{ $notif->pasien->user->name }}</span>.
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            {{ Carbon::parse($notif->tanggal)->format('d-m-Y') }}
                                        </p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-4 text-gray-500 text-center">
                        Tidak ada notifikasi baru.
                    </div>
                @endif
            </div>
        </div>
        
    </div>
@endsection