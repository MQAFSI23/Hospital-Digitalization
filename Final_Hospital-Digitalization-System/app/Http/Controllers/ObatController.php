<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obat;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ObatController extends Controller
{
    public function daftarObat(Request $request)
    {
        $statusKetersediaan = $request->input('status_ketersediaan');
        $query = Obat::query();

        if ($statusKetersediaan) {
            switch ($statusKetersediaan) {
                case 'tersedia':
                    $query->where('stok', '>', 0)
                        ->where(function ($query) {
                            $query->whereNull('kedaluwarsa')
                                ->orWhere('kedaluwarsa', '>', Carbon::now());
                        });
                    break;
                case 'tidak tersedia':
                    $query->where('stok', 0);
                    break;
                case 'kedaluwarsa':
                    $query->whereNotNull('kedaluwarsa')
                        ->where('kedaluwarsa', '<', Carbon::now());
                    break;
            }
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_obat', 'like', '%' . $request->search . '%');
        }

        $daftarObat = $query->get();

        return view('admin.daftarObat', compact('daftarObat'));
    }

    public function detailObat($id) {
        $obat = Obat::findOrFail($id);

        return view('admin.detailObat', compact('obat'));
    }

    public function registerObat() {

        return view('admin.registerObat');
    }

    public function storeObat(Request $request)
    {
        $validatedData = $request->validate([
            'nama_obat' => 'unique:obat,nama_obat|string|max:255',
            'deskripsi' => 'required|string|max:500',
            'tipe_obat' => 'required|in:keras,biasa',
            'stok' => 'required|integer|min:0',
            'gambar_obat' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kedaluwarsa' => 'required|date|after_or_equal:today',
        ]);

        $obat = new Obat();
        $obat->nama_obat = $validatedData['nama_obat'];
        $obat->deskripsi = $validatedData['deskripsi'];
        $obat->tipe_obat = $validatedData['tipe_obat'];
        $obat->stok = $validatedData['stok'];
        $obat->kedaluwarsa = $validatedData['kedaluwarsa'];

        if ($request->hasFile('gambar_obat')) {
            $file = $request->file('gambar_obat');
            $extension = $file->getClientOriginalExtension(); 
            $newFileName = 'gambar_obat_' . $obat->nama_obat . '.' . $extension; 
    
            $file->storeAs('obat_images', $newFileName, 'public');
    
            $obat->gambar_obat = $newFileName;
        }

        $obat->save();

        return redirect()->route('admin.daftarObat')->with('status', 'Obat berhasil didaftarkan.');
    }

    public function editObat($id)
    {
        $obat = Obat::findOrFail($id);

        return view('admin.editObat', compact('obat'));
    }

    public function updateObat(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validatedData = $request->validate([
            'nama_obat' => [
                'string', 'max:255',
                Rule::unique('obat', 'nama_obat')->ignore($obat->id),
            ],
            'deskripsi' => 'required|string|max:500',
            'tipe_obat' => 'required|in:keras,biasa',
            'stok' => 'required|integer|min:0',
            'gambar_obat' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kedaluwarsa' => 'required|date|after_or_equal:today',
        ]);

        if (
            $validatedData['nama_obat'] === $obat->nama_obat &&
            $validatedData['deskripsi'] === $obat->deskripsi &&
            $validatedData['tipe_obat'] === $obat->tipe_obat &&
            $validatedData['stok'] == $obat->stok &&
            $validatedData['kedaluwarsa'] == $obat->kedaluwarsa &&
            !$request->hasFile('gambar_obat')
        ) {
            return redirect()
                ->route('admin.daftarObat', $id)
                ->with('nothing', 'Tidak ada perubahan yang dilakukan.'); 
        }

        if ($request->hasFile('gambar_obat')) {
            $file = $request->file('gambar_obat');
            $extension = $file->getClientOriginalExtension();
            $newFileName = 'gambar_obat_' . $obat->nama_obat . '.' . $extension;
            $file->storeAs('obat_images', $newFileName, 'public');
            $validatedData['gambar_obat'] = $newFileName;
        }

        $obat->update($validatedData);

        return redirect()
            ->route('admin.daftarObat')
            ->with('status', 'Obat berhasil diperbarui.');
    }

    public function hapusObat($id)
    {
        $obat = Obat::findOrFail($id);

        $obat->delete();

        return redirect()->route('admin.daftarObat')->with('status', 'Obat berhasil dihapus.');
    }

}