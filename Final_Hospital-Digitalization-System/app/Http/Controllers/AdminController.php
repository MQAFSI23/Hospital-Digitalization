<?php

namespace App\Http\Controllers;

use App\Models\JadwalTugas;
use App\Models\User;
use App\Models\LogObat;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\Notifikasi;
use App\Models\RekamMedis;
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

        $pasienHariIni = PenjadwalanKonsultasi::with(['pasien', 'dokter'])
            ->whereDate('tanggal_konsultasi', Carbon::today())
            ->get();
        $jumlahPasienHariIni = $pasienHariIni->count();

        $jumlahPengguna = User::count();
        $penggunaTerbaru = User::whereBetween('created_at', [
                Carbon::now()->subDays(30)->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        $jumlahPenggunaTerbaru = User::where('created_at', '>=', Carbon::now()->subMonth())->count();

        $obats = Obat::where('status_kedaluwarsa', 'belum kedaluwarsa')
                    ->where('kedaluwarsa', '<', Carbon::today())
                    ->get();

        foreach ($obats as $obat) {
            $obat->status_kedaluwarsa = 'kedaluwarsa';
            $obat->save();
        }
    
        return view('admin.dashboard', compact(
            'dokterBertugas', 'jumlahPengguna', 'jumlahDokterBertugas', 'pasienHariIni',
                    'jumlahPasienHariIni', 'penggunaTerbaru', 'jumlahPenggunaTerbaru'));
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
        $user = User::with('pasien')->findOrFail($id);
        $dokter = Dokter::with('jadwalTugas')->where('user_id', $id)->first();

        return view('admin.detailPengguna', compact('user', 'dokter'));
    }

    public function editPengguna($id)
    {
        $user = User::with(['dokter.jadwalTugas', 'pasien'])->findOrFail($id);
        $spesialisasiOptions = ['kardiologi', 'neurologi', 'gastroenterologi', 'pediatri', 'pulmonologi'];
        $jenisDokterOptions = ['umum', 'spesialis'];
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $selectedHari = $user->dokter ? $user->dokter->jadwalTugas->pluck('hari_tugas')->toArray() : [];

        return view('admin.editPengguna', compact(
            'user', 'spesialisasiOptions','jenisDokterOptions', 'hariOptions', 'selectedHari'));
    }

    public function updatePengguna(Request $request, $id)
    {
        $user = User::with(['dokter.jadwalTugas', 'pasien'])->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:127'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:pria,wanita'],
            'username' => ['required', 'string', 'max:15', 'unique:users,username,' . $id, 'alpha_num'],
            'role' => ['required', 'in:admin,dokter,pasien'],
            'berat_badan' => ['nullable', 'required_if:role,pasien', 'numeric', 'min:0.1'],
            'tinggi_badan' => ['nullable', 'required_if:role,pasien', 'numeric', 'min:20'],
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

        $pasien = $user->pasien;
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

        $isPasienChanged = $user->role === 'pasien' && $pasien && (
            $pasien->berat_badan !== ($validated['berat_badan'] ?? null) ||
            $pasien->tinggi_badan !== ($validated['tinggi_badan'] ?? null)
        );

        if (!$isUserChanged && !$isDokterChanged && !$isPasienChanged) {
            return redirect()->route('admin.daftarPengguna')->with('nothing', 'Tidak ada perubahan yang dilakukan.');
        }

        if ($user->role !== $validated['role']) {
            if ($user->role === 'dokter' && $dokter) {
                $dokter->jadwalTugas()->delete();
                $dokter->delete();
            }
        
            if ($user->role === 'pasien' && $pasien) {
                $pasien->delete();
            }
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
                    'user_id' => $user->id,
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
        }
        
        elseif ($user->role === 'pasien') {
            if (!$pasien) {
                $pasien = Pasien::create([
                    'user_id' => $user->id,
                    'berat_badan' => $validated['berat_badan'],
                    'tinggi_badan' => $validated['tinggi_badan'],
                ]);
            } else {
                $pasien->update([
                    'berat_badan' => $validated['berat_badan'],
                    'tinggi_badan' => $validated['tinggi_badan'],
                ]);
            }
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

    public function riwayatPeriksa(Request $request)
    {
        $query = RekamMedis::with(['pasien.user', 'dokter.user']);

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('pasien.user', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('dokter.user', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', $searchTerm);
                });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_berobat', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_berobat', '<=', $request->date_to);
        }

        if ($request->filled('sort_by')) {
            $sortOrder = $request->sort_order ?? 'asc';

            if ($request->sort_by === 'name') {
                $query->orderBy(User::select('name')
                    ->whereColumn('users.id', 'rekam_medis.pasien_id'), $sortOrder);
            } elseif ($request->sort_by === 'dokter') {
                $query->orderBy(User::select('name')
                    ->whereColumn('users.id', 'rekam_medis.dokter_id'), $sortOrder);
            } else {
                $query->orderBy('tanggal_berobat', $sortOrder);
            }
        } else {
            $query->orderBy('tanggal_berobat', 'desc');
        }

        $daftarPasien = $query->get();

        return view('admin.riwayatPeriksa', compact('daftarPasien'));
    }

    public function detailRiwayatPeriksa($id)
    {
        $rekamMedis = RekamMedis::with(['pasien', 'dokter', 'resep.obat'])->findOrFail($id);
    
        return view('admin.detailRiwayatPeriksa', compact('rekamMedis'));
    }    

    public function updateResepStatus($rekamMedisId)
    {
        $rekamMedis = RekamMedis::with('resep')->findOrFail($rekamMedisId);
    
        foreach ($rekamMedis->resep as $resep) {
            $resep->update(['status_pengambilan' => true]);
        }

        foreach ($rekamMedis->resep as $resep) {
            LogObat::create([
                'obat_id' => $resep->obat_id,
                'status' => 'terjual',
                'jumlah' => $resep->jumlah,
                'tanggal_log' => now(),
            ]);
        
            $obat = $resep->obat;
            $obat->stok -= $resep->jumlah;
            $obat->save();
        }                
    
        Notifikasi::create([
            'pasien_id' => $rekamMedis->pasien->id,
            'judul' => 'Pengambilan Obat',
            'deskripsi' => 'Obat Anda telah siap diambil.',
            'tanggal' => now(),
            'status' => false, // Belum dibaca
        ]);
    
        return redirect()->route('admin.riwayatPeriksa')->with('status', 'Obat berhasil ditandai selesai dan notifikasi telah dikirimkan.');
    }    
    
}