@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Form Edit Rekam Medis Pasien</div>

        <!-- Session Error -->
        @if (session('stok_error'))
            <div class="mb-4 text-red-600 text-center">
                {{ session('stok_error') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('dokter.updateRekamMedis', $rekamMedis->id) }}">
            @csrf
            @method('PUT')

            <!-- Tindakan -->
            <div class="mb-6 w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="tindakan" :value="__('Tindakan')" />
                <input type="text" id="tindakan" name="tindakan" 
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tindakan', $rekamMedis->tindakan) }}">
                @error('tindakan')
                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Diagnosa -->
            <div class="mb-6 w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="diagnosa" :value="__('Diagnosa')" />
                <input type="text" id="diagnosa" name="diagnosa" 
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('diagnosa', $rekamMedis->diagnosa) }}" required>
                @error('diagnosa')
                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tanggal Berobat -->
            <div class="mb-6 w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="tanggal_berobat" :value="__('Tanggal Konsultasi')" />
                <input type="date" id="tanggal_berobat" name="tanggal_berobat"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tanggal_berobat', $rekamMedis->tanggal_berobat) }}" required>
                @error('tanggal_berobat')
                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Obat Section -->
            <div id="obatSection">
                @foreach ($rekamMedis->resep as $key => $obat)
                    <div class="mb-6 obat-item">
                        <select name="obat_id[]" class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                            <option value="">Pilih Obat</option>
                            @foreach ($obats as $obatOption)
                                <option value="{{ $obatOption->id }}" 
                                    {{ in_array($obatOption->id, old('obat_id', $rekamMedis->resep->pluck('obat_id')->toArray())) && $obatOption->id == $rekamMedis->resep[$key]->obat_id ? 'selected' : '' }}>
                                    {{ $obatOption->nama_obat }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-label for="dosis[]" :value="__('Dosis')" />
                        <input type="text" name="dosis[]" 
                            class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                            value="{{ old('dosis')[$key] ?? $obat->dosis }}" required>

                        <x-input-label for="jumlah[]" :value="__('Jumlah')" />
                        <input type="number" name="jumlah[]" min="0" 
                            class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                            value="{{ old('jumlah')[$key] ?? $obat->jumlah }}" required>

                        <x-input-label for="aturan_pakai[]" :value="__('Aturan Pakai')" />
                        <input type="text" name="aturan_pakai[]" 
                            class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                            value="{{ old('aturan_pakai')[$key] ?? $obat->aturan_pakai }}" required>

                        <button type="button" class="hapusObatBtn mt-2 text-white bg-red-500 duration-300  px-4 py-1 rounded hover:bg-red-700">
                            Hapus Obat
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Tambah Obat -->
            <button type="button" id="tambahObatBtn"
                class="block w-full text-white bg-gray-500 px-4 py-2 rounded hover:bg-gray-700 duration-300">
                Tambah Obat
            </button>

            <!-- Submit -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('dokter.detailPasien', $rekamMedis->pasien_id) }}" 
                    class="text-indigo-600 hover:underline">
                    Kembali
                </a>
                <button type="submit"
                    class="text-white bg-indigo-600 px-4 py-2 rounded hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </form>
    </section>

    <!-- Script Tambah/Hapus Obat -->
    <script>
        document.getElementById('tambahObatBtn').addEventListener('click', function() {
            const obatSection = document.getElementById('obatSection');
            const newObat = `
                <div class="mb-6 obat-item">
                    <x-input-label for="obat_id[]" :value="__('Obat')" />
                    <select name="obat_id[]" class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                        <option value="">Pilih Obat</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                        @endforeach
                    </select>
                    <x-input-label for="dosis[]" :value="__('Dosis')" />
                    <input type="text" name="dosis[]" 
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                    <x-input-label for="jumlah[]" :value="__('Jumlah')" />
                    <input type="number" name="jumlah[]" min="0" 
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                    <x-input-label for="aturan_pakai[]" :value="__('Aturan Pakai')" />
                    <input type="text" name="aturan_pakai[]" 
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                    <button type="button" class="hapusObatBtn mt-2 text-white bg-red-600 px-4 py-1 rounded hover:bg-red-700">
                        Hapus Obat
                    </button>
                </div>
            `;
            obatSection.insertAdjacentHTML('beforeend', newObat);
        });

        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('hapusObatBtn')) {
                e.target.closest('.obat-item').remove();
            }
        });
    </script>
@endsection