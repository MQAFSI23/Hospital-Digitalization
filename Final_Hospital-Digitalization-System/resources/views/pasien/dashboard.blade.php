@extends('layouts.app2')

@section('title', 'Dashboard')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="mt-4 text-gray-600">Halo, {{ auth()->user()->name }}👋</p>

        @if(session('status'))
            <div class="p-4 mt-4 text-sm text-green-500 bg-green-100 rounded">
                {{ session('status') }}
            </div>
        @elseif(session('error'))
            <div class="p-4 mt-4 text-sm text-red-500 bg-red-100 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Notifikasi -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800">Notifikasi</h2>
            <div class="mt-4 bg-white shadow rounded">
                @if($notifikasi->isNotEmpty())
                    <ul class="divide-y divide-gray-200">
                        @foreach($notifikasi as $notif)
                            <li class="p-4 hover:bg-gray-100">
                                <a href="{{ route('pasien.detailNotifikasi', $notif->id) }}" class="block">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-semibold text-indigo-700">
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

        <!-- Daftar Dokter -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Dokter</h2>

            <!-- Dokter Umum -->
            <div class="mt-4">
                <h3 class="text-xl font-semibold text-gray-700">Dokter Umum ({{ $jumlahDokterUmum }} Dokter)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    @foreach($dokterUmum as $dokter)
                        <div class="bg-white p-4 shadow rounded-lg hover:bg-gray-100">
                            <p class="text-lg font-semibold text-indigo-700">{{ $dokter->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ ucfirst($dokter->jenis_dokter) }}</p>
                            <button 
                                class="mt-2 inline-block text-indigo-600 hover:underline"
                                data-bs-toggle="modal" data-bs-target="#janjiModal"
                                data-dokter-id="{{ $dokter->id }}" 
                                data-dokter-name="{{ $dokter->user->name }}">
                                Buat Janji Konsultasi
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Dokter Spesialis -->
            @foreach($dokterSpesialisasi as $spesialisasi => $dokters)
                <div class="mt-8">
                    <h3 class="text-xl font-semibold text-gray-700">Spesialis {{ ucwords($spesialisasi) }} ({{ $jumlahDokterSpesialis[$spesialisasi] }} Dokter)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @foreach($dokters as $dokter)
                            <div class="bg-white p-4 shadow rounded-lg hover:bg-gray-100">
                                <p class="text-lg font-semibold text-indigo-700">{{ $dokter->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($dokter->jenis_dokter) }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($dokter->spesialisasi) }}</p>
                                <button 
                                    class="mt-2 inline-block text-indigo-600 hover:underline"
                                    data-bs-toggle="modal" data-bs-target="#janjiModal"
                                    data-dokter-id="{{ $dokter->id }}" 
                                    data-dokter-name="{{ $dokter->user->name }}">
                                    Buat Janji Konsultasi
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- Modal -->
    <div id="janjiModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg w-96 p-8 mx-4">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-semibold" id="janjiModalLabel">Buat Janji Konsultasi</h5>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="mt-4">
                <form action="{{ route('pasien.janjiKonsultasiStore') }}" method="POST">
                    @csrf
                    <input type="hidden" name="dokter_id" id="dokter_id">
                    <div class="mb-4">
                        <label for="dokter_name" class="block text-sm font-medium text-gray-700">Dokter</label>
                        <input type="text" class="mt-1 block w-full p-2 border rounded-md bg-gray-100" id="dokter_name" readonly>
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_konsultasi" class="block text-sm font-medium text-gray-700">Pilih Tanggal (maksimal dua pekan ke depan)</label>
                        <select name="tanggal_konsultasi" id="tanggal_konsultasi" class="form-control">
                            <!-- Opsi tanggal akan diisi melalui JavaScript -->
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-700 duration-300 text-white py-2 rounded">Buat Janji</button>
                </form>
            </div>
        </div>
    </div>

    <script>

        const modal = document.getElementById('janjiModal');
        const closeModal = document.getElementById('closeModal');

        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function (event) {
                const dokterId = event.target.getAttribute('data-dokter-id');
                const dokterName = event.target.getAttribute('data-dokter-name');
                
                document.getElementById('dokter_name').value = dokterName;
                document.getElementById('dokter_id').value = dokterId;

                modal.classList.remove('hidden');

                fetch(`/dokter/${dokterId}/jadwal`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('tanggal_konsultasi');
                        select.innerHTML = '';
                        if (data.availableDates.length > 0) {
                            data.availableDates.forEach(date => {
                                const option = document.createElement('option');
                                option.value = date.split(' ')[0];
                                option.textContent = date;
                                select.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.textContent = 'Tidak ada jadwal tersedia';
                            option.disabled = true;
                            select.appendChild(option);
                        }
                    })
                    .catch(error => console.error('Error:', error)); 
            });
        });

        closeModal.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>

@endsection