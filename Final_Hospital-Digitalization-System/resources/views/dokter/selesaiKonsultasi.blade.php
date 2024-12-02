@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Form Tindakan Pasien</div>

        <!-- Session Error -->
        @if ($errors->has('stok_error'))
            <div class="mb-4 text-red-600 text-center">
                {{ $errors->first('stok_error') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('dokter.selesaiStore', $penjadwalan->id) }}">
            @csrf
            <!-- Tindakan -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500">
                <x-input-label for="tindakan" :value="__('Tindakan')" />
                <input type="text" id="tindakan" name="tindakan" placeholder="Tindakan"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tindakan') }}">
            </div>
            @error('tindakan')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Diagnosa -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="diagnosa" :value="__('Diagnosa')" />
                <input type="text" id="diagnosa" name="diagnosa" placeholder="Diagnosa"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('diagnosa') }}" required>
            </div>
            @error('diagnosa')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Tanggal Berobat -->
            <div class="w-full transform border-b-2 bg-transparent text-lg duration-300 focus-within:border-indigo-500 mt-6">
                <x-input-label for="tanggal_berobat" :value="__('Tanggal Konsultasi')" />
                <input type="date" id="tanggal_berobat" name="tanggal_berobat" placeholder="Tanggal Konsultasi"
                    class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                    value="{{ old('tanggal_berobat') }}" required>
            </div>
            @error('tanggal_berobat')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
            @enderror

            <!-- Obat Section -->
            <div id="obatSection">
                @if (old('obat_id'))
                    @foreach (old('obat_id') as $key => $obatId)
                        <div class="mt-6">
                            <x-input-label for="obat_id[]" :value="__('Obat')" />
                            <select name="obat_id[]" class="w-full border-none bg-transparent outline-none" required>
                                <option value="">Pilih Obat</option>
                                @foreach ($obats as $obat)
                                    <option value="{{ $obat->id }}" 
                                        {{ $obat->id == $obatId ? 'selected' : '' }}>
                                        {{ $obat->nama_obat }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label for="dosis[]" :value="__('Dosis')" />
                            <input type="text" id="dosis" name="dosis[]" placeholder="Dosis"
                                class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                                value="{{ old('dosis')[$key] }}" required>
                            
                            <x-input-label for="jumlah[]" :value="__('Jumlah')" />
                            <input type="number" id="jumlah" name="jumlah[]" placeholder="Jumlah" min="1"
                                class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                                value="{{ old('jumlah')[$key] }}" required>
                            
                            <x-input-label for="aturan_pakai[]" :value="__('Aturan Pakai')" />
                            <input type="text" id="aturan_pakai" name="aturan_pakai[]" placeholder="Aturan Pakai"
                                class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none"
                                value="{{ old('aturan_pakai')[$key] }}" required>
                            
                            <!-- Tombol Hapus -->
                            <button type="button"
                                class="hapusObatBtn mt-2 rounded-sm bg-red-500 text-white py-1 px-2 font-bold duration-300 hover:bg-red-700">
                                Hapus Obat
                            </button>

                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Tambah Obat -->
            <div class="mt-6">
                <button type="button" id="tambahObatBtn"
                    class="rounded-sm bg-gray-500 py-2 px-2 font-bold duration-300 hover:bg-gray-700">
                    Tambah Obat
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-8">
                <!-- Back -->
                <a class="text-lg font-medium text-indigo-500 hover:underline transition-all duration-300"
                    href="{{ route('dokter.dashboard') }}">
                    {{ __('Kembali') }}
                </a>

                <!-- Simpan -->
                <button type="submit"
                    class="transform rounded-sm bg-indigo-500 py-2 px-2 font-bold duration-300 hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </form>
    </section>

    <script>
        document.getElementById('tambahObatBtn').addEventListener('click', function() {
            const obatSection = document.getElementById('obatSection');
            const newObat = `
                <div class="mt-6 obat-item">
                    <x-input-label for="obat_id[]" :value="__('Obat')" />
                    <select name="obat_id[]" class="w-full border-none bg-transparent outline-none" required>
                        <option value="">Pilih Obat</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                        @endforeach
                    </select>

                    <x-input-label for="dosis[]" :value="__('Dosis')" />
                    <input type="text" id="dosis" name="dosis[]" placeholder="Dosis"
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                    
                    <x-input-label for="jumlah[]" :value="__('Jumlah')" />
                    <input type="number" id="jumlah" name="jumlah[]" placeholder="Jumlah" min="1"
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>
                    
                    <x-input-label for="aturan_pakai[]" :value="__('Aturan Pakai')" />
                    <input type="text" id="aturan_pakai" name="aturan_pakai[]" placeholder="Aturan Pakai"
                        class="w-full border-none bg-transparent outline-none placeholder:italic focus:outline-none" required>

                    <!-- Tombol Hapus -->
                    <button type="button" class="hapusObatBtn mt-2 rounded-sm bg-red-500 text-white py-1 px-2 font-bold duration-300 hover:bg-red-700">
                        Hapus Obat
                    </button>
                </div>
            `;

            const div = document.createElement('div');
            div.innerHTML = newObat;
            obatSection.appendChild(div);

            div.querySelector('.hapusObatBtn').addEventListener('click', function() {
                div.remove();
            });

            document.querySelectorAll('.hapusObatBtn').forEach(button => {
                button.addEventListener('click', function() {
                    button.closest('.obat-item').remove();
                });
            });

        });

    </script>

@endsection