<?php

namespace App\Http\Controllers;

use App\Models\JadwalTugas;
use App\Models\User;
use App\Models\Dokter;
use App\Models\PenjadwalanKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $jumlahPenggunaTerbaru = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $obats = Obat::where('status_kedaluwarsa', 'belum kedaluwarsa')
                    ->where('kedaluwarsa', '<', Carbon::today())
                    ->get();

        foreach ($obats as $obat) {
            $obat->status_kedaluwarsa = 'kedaluwarsa';
            $obat->save();
        }
    
        return view('admin.dashboard', compact('dokterBertugas', 'jumlahPengguna', 'jumlahDokterBertugas', 'jumlahPasienHariIni', 'penggunaTerbaru', 'jumlahPenggunaTerbaru'));
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
        $dokter = Dokter::with('jadwalTugas')->where('dokter_id', $id)->first();

        return view('admin.detailPengguna', compact('user', 'dokter'));
    }

    public function editPengguna($id)
    {
        $user = User::with(['dokter.jadwalTugas'])->findOrFail($id);
        $spesialisasiOptions = ['kardiologi', 'neurologi', 'gastroenterologi', 'pediatri', 'pulmonologi'];
        $jenisDokterOptions = ['umum', 'spesialis'];
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $selectedHari = $user->dokter ? $user->dokter->jadwalTugas->pluck('hari_tugas')->toArray() : [];

        return view('admin.editPengguna', compact(
            'user', 'spesialisasiOptions','jenisDokterOptions', 'hariOptions', 'selectedHari'));
    }

    public function updatePengguna(Request $request, $id)
    {
        $user = User::with(['dokter.jadwalTugas'])->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:127'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:pria,wanita'],
            'username' => ['required', 'string', 'max:15', 'unique:users,username,' . $id, 'alpha_num'],
            'role' => ['required', 'in:admin,dokter,pasien'],
            'jenis_dokter' => ['nullable', 'required_if:role,dokter', 'in:umum,spesialis'],
            'spesialisasi' => ['nullable', 'required_if:jenis_dokter,spesialis', 'in:kardiologi,neurologi,gastroenterologi,pediatri,pulmonologi'],
            'jadwal_tugas' => ['nullable', 'required_if:role,dokter', 'array'],
            'jadwal_tugas.*' => ['in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'admin_password' => ['required'],
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->admin_password, $admin->password)) {

            return redirect()->route('admin.daftarPengguna')->with('error', 'Password admin tidak valid. Tidak ada perubahan yang dilakukan.');
        }

        $dokter = $user->dokter;

        $isUserChanged = $user->name !== $validated['name'] ||
            $user->tanggal_lahir !== $validated['tanggal_lahir'] ||
            $user->jenis_kelamin !== $validated['jenis_kelamin'] ||
            $user->username !== $validated['username'] ||
            $user->role !== $validated['role'];

        $isDokterChanged = $user->role === 'dokter' && $dokter && (
            $dokter->jenis_dokter !== ($validated['jenis_dokter'] ?? null) ||
            $dokter->spesialisasi !== ($validated['spesialisasi'] ?? null) ||
            $dokter->jadwalTugas->pluck('hari_tugas')->sort()->toArray() !== collect($validated['jadwal_tugas'] ?? [])->sort()->toArray()
        );

        if (!$isUserChanged && !$isDokterChanged) {
            return redirect()->route('admin.daftarPengguna')->with('nothing', 'Tidak ada perubahan yang dilakukan.');
        }

        $user->update([
            'name' => $validated['name'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'username' => $validated['username'],
            'role' => $validated['role'],
        ]);

        if ($user->role === 'dokter') {
            if (!$dokter) {
                $dokter = Dokter::create([
                    'dokter_id' => $user->id,
                    'jenis_dokter' => $validated['jenis_dokter'],
                    'spesialisasi' => $validated['spesialisasi'],
                ]);
            } else {
                $dokter->update([
                    'jenis_dokter' => $validated['jenis_dokter'],
                    'spesialisasi' => $validated['spesialisasi'],
                ]);
            }
    
            if (!empty($validated['jadwal_tugas'])) {
                $currentJadwalTugas = $dokter->jadwalTugas->pluck('hari_tugas')->toArray();
                $newJadwalTugas = $validated['jadwal_tugas'];
    
                foreach ($newJadwalTugas as $hari) {
                    if (!in_array($hari, $currentJadwalTugas)) {
                        $dokter->jadwalTugas()->create(['hari_tugas' => $hari]);
                    }
                }
    
                foreach ($currentJadwalTugas as $hari) {
                    if (!in_array($hari, $newJadwalTugas)) {
                        $dokter->jadwalTugas()->where('hari_tugas', $hari)->delete();
                    }
                }
            }

        } elseif ($dokter) {
            $dokter->jadwalTugas()->delete();
            $dokter->delete();
        }

        return redirect()->route('admin.daftarPengguna')->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function hapusPengguna(Request $request, $id)
    {
        $request->validate([
            'password' => 'required',
        ]);
        
        $user = User::findOrFail($id);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('admin.daftarPengguna')->with('error', 'Password admin tidak valid. Pengguna gagal dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.daftarPengguna')->with('status', 'Pengguna berhasil dihapus.');
    }
    
}