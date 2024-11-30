@extends('layouts.auth-layout')

@section('content')
    <section class="flex flex-col w-full max-w-lg px-8 sm:px-8 lg:px-8 space-y-10">
        <!-- Judul Halaman -->
        <div class="text-center text-4xl font-medium">Selesai Berobat</div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-500">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form untuk Menyimpan Rekam Medis dan Resep -->
        <form action="{{ route('dokter.selesai', $rekamMedis->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="tindakan" class="form-label">Tindakan</label>
                <input type="text" class="form-control" id="tindakan" name="tindakan" value="{{ old('tindakan') }}" required>
            </div>

            <div class="mb-3">
                <label for="diagnosa" class="form-label">Diagnosa</label>
                <input type="text" class="form-control" id="diagnosa" name="diagnosa" value="{{ old('diagnosa') }}" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_berobat" class="form-label">Tanggal Berobat</label>
                <input type="date" class="form-control" id="tanggal_berobat" name="tanggal_berobat" value="{{ old('tanggal_berobat') }}" required>
            </div>

            <!-- Obat, Dosis, dan Jumlah -->
            <div id="obatSection">
                <div class="mb-3 obat-row">
                    <label for="obat_id[]" class="form-label">Obat</label>
                    <select name="obat_id[]" class="form-control obat-select" required>
                        <option value="">Pilih Obat</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 dosis-row">
                    <label for="dosis[]" class="form-label">Dosis</label>
                    <input type="text" class="form-control" name="dosis[]" required>
                </div>
                <div class="mb-3 jumlah-row">
                    <label for="jumlah[]" class="form-label">Jumlah</label>
                    <input type="number" class="form-control" name="jumlah[]" min="1" required>
                </div>
                <div class="mb-3 aturan-pakai-row">
                    <label for="aturan_pakai[]" class="form-label">Aturan Pakai</label>
                    <input type="text" class="form-control" name="aturan_pakai[]" required>
                </div>
            </div>

            <!-- Tombol untuk menambah obat -->
            <button type="button" class="btn btn-secondary" id="tambahObatBtn">Tambah Obat</button>

            <div class="flex justify-between mt-4">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Selesai</button>
            </div>
        </form>
    </section>

    <script>
        // Menambah baris input untuk obat
        document.getElementById('tambahObatBtn').addEventListener('click', function() {
            var obatSection = document.getElementById('obatSection');
            var obatRow = document.createElement('div');
            obatRow.classList.add('mb-3', 'obat-row');
            obatRow.innerHTML = `
                <label for="obat_id[]" class="form-label">Obat</label>
                <select name="obat_id[]" class="form-control obat-select" required>
                    <option value="">Pilih Obat</option>
                    @foreach ($obats as $obat)
                        <option value="{{ $obat->id }}">{{ $obat->nama }}</option>
                    @endforeach
                </select>
            `;
            var dosisRow = document.createElement('div');
            dosisRow.classList.add('mb-3', 'dosis-row');
            dosisRow.innerHTML = `
                <label for="dosis[]" class="form-label">Dosis</label>
                <input type="text" class="form-control" name="dosis[]" required>
            `;
            var jumlahRow = document.createElement('div');
            jumlahRow.classList.add('mb-3', 'jumlah-row');
            jumlahRow.innerHTML = `
                <label for="jumlah[]" class="form-label">Jumlah</label>
                <input type="number" class="form-control" name="jumlah[]" min="1" required>
            `;
            var aturanPakaiRow = document.createElement('div');
            aturanPakaiRow.classList.add('mb-3', 'aturan-pakai-row');
            aturanPakaiRow.innerHTML = `
                <label for="aturan_pakai[]" class="form-label">Aturan Pakai</label>
                <input type="text" class="form-control" name="aturan_pakai[]" required>
            `;
            
            // Menambahkan baris baru
            obatSection.appendChild(obatRow);
            obatSection.appendChild(dosisRow);
            obatSection.appendChild(jumlahRow);
            obatSection.appendChild(aturanPakaiRow);
        });
    </script>
@endsection