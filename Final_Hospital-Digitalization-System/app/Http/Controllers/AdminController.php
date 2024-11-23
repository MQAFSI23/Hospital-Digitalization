<?php

namespace App\Http\Controllers;

use App\Models\JadwalTugas;
use App\Models\User;
use App\Models\Dokter;
use App\Models\PenjadwalanKonsultasi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Obat;

class AdminController extends Controller
{
    public function dashboard()
    {
        $dokterBertugas = JadwalTugas::with('dokter')
            ->where('hari_tugas', Carbon::now()->isoFormat('dddd'))
            ->get();
        $jumlahDokterBertugas = JadwalTugas::where('hari_tugas', Carbon::now()->isoFormat('dddd'))->count();
        $jumlahPengguna = User::count();
        $jumlahPasienHariIni = PenjadwalanKonsultasi::whereDate('tanggal_konsultasi', Carbon::today())->count();
        $penggunaTerbaru = User::where('created_at', '>=', Carbon::now()->subMonth())->get();

        $obats = Obat::where('status_kedaluwarsa', 'belum kedaluwarsa')
                    ->where('kedaluwarsa', '<', Carbon::today())
                    ->get();

        foreach ($obats as $obat) {
            $obat->status_kedaluwarsa = 'kedaluwarsa';
            $obat->save();
        }
    
        return view('admin.dashboard', compact('dokterBertugas', 'jumlahPengguna', 'jumlahDokterBertugas', 'jumlahPasienHariIni', 'penggunaTerbaru'));
    }

    public function daftarPengguna(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order ?: 'asc';

            if (in_array($sortBy, ['name', 'role', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $daftarPengguna = $query->get();

        return view('admin.daftarPengguna', compact('daftarPengguna'));
    }

    public function detailPengguna($id)
    {
        $user = User::findOrFail($id);
        $dokter = Dokter::where('dokter_id', $id)->first();

        return view('admin.detailPengguna', compact('user', 'dokter'));
    }

    public function editPengguna($id)
    {
        $user = User::findOrFail($id);
        return view('admin.editPengguna', compact('user'));
    }

    public function updatePengguna(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:127'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:pria,wanita'],
            'username' => ['required', 'string', 'max:15', 'unique:users,username,' . $id, 'alpha_num'],
        ]);

        if (
            $user->name === $validated['name'] &&
            $user->tanggal_lahir === $validated['tanggal_lahir'] &&
            $user->jenis_kelamin === $validated['jenis_kelamin'] &&
            $user->username === $validated['username']
        ) {
            return redirect()->route('admin.daftarPengguna')->with('nothing', 'Tidak ada perubahan yang dilakukan.');
        }

        $user->update($validated);

        return redirect()->route('admin.daftarPengguna')->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function hapusPengguna($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.daftarPengguna')->with('status', 'Pengguna berhasil dihapus.');
    }
    
}