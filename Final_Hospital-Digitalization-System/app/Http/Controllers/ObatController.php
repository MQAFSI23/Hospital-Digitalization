<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obat;
use App\Models\LogObat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

        if ($request->has('sort_by') && $request->sort_by != '') {
            $validSortColumns = ['nama_obat', 'stok', 'kedaluwarsa'];
            $sortBy = in_array($request->sort_by, $validSortColumns) ? $request->sort_by : 'nama_obat';
    
            $sortOrder = $request->has('sort_order') && $request->sort_order === 'desc' ? 'desc' : 'asc';
    
            $query->orderBy($sortBy, $sortOrder);
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
            'kedaluwarsa' => 'required|date|after_or_equal:'. now()->addMonths(3)->format('d-m-Y'),
            'admin_password' => 'required',
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->admin_password, $admin->password)) {

            return redirect()->route('admin.daftarObat')->with('error', 'Password admin tidak valid. Obat gagal didaftar.');
        }

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

        if ($obat->stok > 0) {
            LogObat::create([
                'obat_id' => $obat->id,
                'status' => 'terisi',
                'jumlah' => $obat->stok,
                'tanggal_log' => now(),
            ]);
        }

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
            'kedaluwarsa' => 'required|date|after_or_equal:'. now()->addMonths(3)->format('d-m-Y'),
            'admin_password' => 'required',
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->admin_password, $admin->password)) {

            return redirect()->route('admin.daftarObat')->with('error', 'Password admin tidak valid. Tidak ada perubahan yang dilakukan.');
        }

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

        if ($validatedData['stok'] != $obat->stok) {
            $jumlahPerubahan = $validatedData['stok'] - $obat->stok;
            LogObat::create([
                'obat_id' => $obat->id,
                'status' => $jumlahPerubahan > 0 ? 'terisi' : 'terjual',
                'jumlah' => abs($jumlahPerubahan),
                'tanggal_log' => now(),
            ]);
        }

        $obat->update($validatedData);

        return redirect()
            ->route('admin.daftarObat')
            ->with('status', 'Data obat berhasil diperbarui.');
    }

    public function hapusObat(Request $request, $id)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $obat = Obat::findOrFail($id);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('admin.daftarObat')->with('error', 'Password admin tidak valid. Obat gagal dihapus.');
        }

        $obat->delete();

        return redirect()->route('admin.daftarObat')->with('status', 'Obat berhasil dihapus.');
    }

    public function logObat(Request $request)
    {
        $query = LogObat::query();

        if ($request->filled('search')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_awal')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $query->where('tanggal_log', '>=', $tanggalAwal);
        }

        if ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->where('tanggal_log', '<=', $tanggalAkhir);
        }

        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order ? : 'desc';
            
            if ($sortBy == 'nama_obat') {
                $query->join('obat', 'log_obat.obat_id', '=', 'obat.id')
                    ->orderBy('obat.nama_obat', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $logObat = $query->with('obat')->get();

        return view('admin.logObat', compact('logObat'));
    }

}