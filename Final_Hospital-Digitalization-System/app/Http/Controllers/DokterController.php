<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use App\Models\PenjadwalanKonsultasi;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokterId = auth()->user()->dokter->id;
        $jadwalDokter = auth()->user()->dokter->jadwalTugas;

        $pasienSelesai = RekamMedis::with('pasien')
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal_berobat', Carbon::today())
            ->whereIn(DB::raw('DAYOFWEEK(tanggal_berobat)'), $this->getHariTugasAsDayOfWeek($jadwalDokter))
            ->get();

        $totalPasienSelesai = $pasienSelesai->count();

        $pasienKonsul = PenjadwalanKonsultasi::with('pasien')
            ->where('status', 'belum')
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal_konsultasi', Carbon::today())
            ->whereIn(DB::raw('DAYOFWEEK(tanggal_konsultasi)'), $this->getHariTugasAsDayOfWeek($jadwalDokter))
            ->get();
    
        $pasienKonsul = $pasienKonsul->map(function ($penjadwalan) {
            $penjadwalan->rekamMedis = RekamMedis::where('pasien_id', $penjadwalan->pasien->id)
                                                ->whereDate('tanggal_berobat', Carbon::today())
                                                ->first();
            return $penjadwalan;
        });

        $totalKonsul = $pasienKonsul->count();
        
        return view('dokter.dashboard', compact(
            'pasienSelesai', 'pasienKonsul', 'totalKonsul', 'totalPasienSelesai', 'jadwalDokter'));
    }

    /**
     * Mengubah hari tugas menjadi format DAYOFWEEK
     */
    private function getHariTugasAsDayOfWeek($jadwalDokter)
    {
        $hariTugas = $jadwalDokter->pluck('hari_tugas')->toArray();
        $hariIndo = [
            'Senin' => 2,
            'Selasa' => 3,
            'Rabu' => 4,
            'Kamis' => 5,
            'Jumat' => 6,
            'Sabtu' => 7,
            'Minggu' => 1,
        ];

        return array_map(function($hari) use ($hariIndo) {
            return $hariIndo[$hari] ?? null; // Mengubah hari Indonesia ke format DAYOFWEEK (1-7)
        }, $hariTugas);
    }

    public function selesaiKonsultasi(PenjadwalanKonsultasi $penjadwalan)
    {
        $obats = Obat::all();

        return view('dokter.selesaiKonsultasi', compact('penjadwalan', 'obats'));
    }

    public function selesaiStore(Request $request, PenjadwalanKonsultasi $penjadwalan)
    {
        $request->validate([
            'tindakan' => 'nullable|string',
            'diagnosa' => 'required|string',
            'tanggal_berobat' => 'required|date|before_or_equal:today',
            'obat_id.*' => 'nullable|exists:obat,id',
            'dosis.*' => 'nullable|string',
            'jumlah.*' => 'nullable|integer|min:1',
            'aturan_pakai.*' => 'nullable|string',
        ]);

        if (!empty($request->obat_id)) {
            foreach ($request->obat_id as $key => $obatId) {
                $obat = Obat::find($obatId);
                if ($request->jumlah[$key] > $obat->stok) {
                    return back()
                        ->withErrors(['stok_error' => 'Jumlah obat ' . $obat->nama_obat . ' melebihi stok yang tersedia.'])
                        ->withInput();
                }
            }
        }

        $rekamMedis = RekamMedis::create([
            'pasien_id' => $penjadwalan->pasien_id,
            'dokter_id' => $penjadwalan->dokter_id,
            'tindakan' => $request->tindakan ?? null,
            'diagnosa' => $request->diagnosa,
            'tanggal_berobat' => $request->tanggal_berobat,
            'created_by' => auth()->user()->dokter->id,
        ]);

        if (!empty($request->obat_id)) {
            $resepData = [];
            foreach ($request->obat_id as $key => $obatId) {
                $resepData[] = [
                    'rekam_medis_id' => $rekamMedis->id,
                    'obat_id' => $obatId,
                    'dosis' => $request->dosis[$key],
                    'jumlah' => $request->jumlah[$key],
                    'aturan_pakai' => $request->aturan_pakai[$key],
                    'created_by' => auth()->user()->dokter->id,
                ];
            }

            Resep::insert($resepData);
        }

        if (!empty($request->tindakan)) {
            Notifikasi::create([
                'pasien_id' => $rekamMedis->pasien->id,
                'judul' => 'Tindakan dari ' . auth()->user()->name,
                'deskripsi' => 'Dokter memberikan tindakan: ' . $request->tindakan,
                'tanggal' => now(),
                'status' => false,
            ]);
        } 

        $penjadwalan->status = 'selesai';
        $penjadwalan->save();

        $message = empty($request->obat_id) 
            ? 'Diagnosa berhasil disimpan. Tidak ada resep yang diberikan.'
            : 'Resep dan diagnosa berhasil disimpan.';
        
        return redirect()->route('dokter.dashboard')->with('status', $message);
    }

    public function editRekamMedis($id)
    {
        $rekamMedis = RekamMedis::with('resep.obat')->findOrFail($id);
    
        $obats = Obat::all();
    
        return view('dokter.editRekamMedis', compact('rekamMedis', 'obats'));
    }

    public function updateRekamMedis(Request $request, $id)
    {
        $request->validate([
            'tindakan' => 'nullable|string|max:255',
            'diagnosa' => 'required|string|max:255',
            'tanggal_berobat' => 'required|date',
            'obat_id.*' => 'nullable|exists:obat,id',
            'dosis.*' => 'nullable|string|max:100',
            'jumlah.*' => 'nullable|integer|min:0',
        ]);
    
        $rekamMedis = RekamMedis::with('resep')->findOrFail($id);
    
        // Periksa perubahan di rekam medis
        $isRekamMedisChanged = false;
        if ($rekamMedis->tindakan !== $request->tindakan || 
            $rekamMedis->diagnosa !== $request->diagnosa || 
            $rekamMedis->tanggal_berobat !== $request->tanggal_berobat) {
            $isRekamMedisChanged = true;
        }
    
        $existingResep = $rekamMedis->resep->keyBy('obat_id')->toArray();  // Key by obat_id untuk memudahkan pencarian
    
        $isResepChanged = false;
    
        $newResep = [];
        if ($request->obat_id) {  // Pastikan obat_id ada dalam request
            foreach ($request->obat_id as $key => $obatId) {
                if ($obatId) {  // Pastikan obatId tidak kosong
                    $newResep[$obatId] = [
                        'obat_id' => $obatId,
                        'dosis' => $request->dosis[$key],
                        'jumlah' => $request->jumlah[$key],
                        'aturan_pakai' => $request->aturan_pakai[$key],
                    ];
                }
            }
        }
    
        $deletedResep = [];
        $updatedResep = [];
    
        foreach ($existingResep as $obatId => $resep) {
            if (!isset($newResep[$obatId])) {
                $deletedResep[$obatId] = $resep;
                $isResepChanged = true; // Tandai perubahan jika ada yang dihapus
            } else {
                // Cek apakah ada perubahan dalam resep
                if ($resep['dosis'] !== $newResep[$obatId]['dosis'] ||
                    $resep['jumlah'] !== $newResep[$obatId]['jumlah'] ||
                    $resep['aturan_pakai'] !== $newResep[$obatId]['aturan_pakai']) {
                    $updatedResep[$obatId] = $newResep[$obatId];
                    $isResepChanged = true; // Tandai perubahan jika ada yang diubah
                }
            }
        }
    
        foreach ($newResep as $obatId => $resep) {
            if (!isset($existingResep[$obatId])) {
                // Obat baru ditambahkan
                $updatedResep[$obatId] = $resep;
                $isResepChanged = true; // Tandai perubahan jika ada obat baru
            }
        }
    
        if (!$isRekamMedisChanged && !$isResepChanged) {
            return redirect()->route('dokter.detailPasien', $rekamMedis->pasien_id)
                            ->with('nothing', 'Tidak ada perubahan yang dibuat.');
        }
    
        foreach ($newResep as $key => $resep) {
            $obat = Obat::find($resep['obat_id']);
            if ($obat->stok < $resep['jumlah']) {
                return redirect()->back()->with('stok_error', "Stok obat {$obat->nama_obat} tidak mencukupi.");
            }
        }

        if ($rekamMedis->tindakan !== $request->tindakan && !empty($request->tindakan)) {
            Notifikasi::create([
                'pasien_id' => $rekamMedis->pasien->id,
                'judul' => 'Perubahan tindakan dari ' . auth()->user()->name,
                'deskripsi' => 'Dokter memberikan tindakan: ' . $request->tindakan,
                'tanggal' => now(),
                'status' => false,
            ]);
        }
    
        $rekamMedis->update($request->only('tindakan', 'diagnosa', 'tanggal_berobat'));
    
        // Hapus resep yang dihapus
        foreach ($deletedResep as $obatId => $resep) {
            $rekamMedis->resep()->where('obat_id', $obatId)->delete();
        }
    
        // Tambahkan atau perbarui resep
        foreach ($updatedResep as $resep) {
            $rekamMedis->resep()->updateOrCreate(
                ['obat_id' => $resep['obat_id']], // Cari berdasarkan obat_id
                $resep // Update atau buat dengan data baru
            );
        }
    
        return redirect()->route('dokter.detailPasien', $rekamMedis->pasien_id)
                        ->with('status', 'Rekam medis berhasil diperbarui.');
    }
                
    
    public function daftarPasien(Request $request)
    {
        $dokterId = auth()->user()->dokter->id;

        $query = RekamMedis::with('pasien.user')
            ->where('dokter_id', $dokterId);

        if ($request->filled('search')) {
            $query->whereHas('pasien.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
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
                $query->join('users', 'rekam_medis.pasien_id', '=', 'users.id')
                    ->orderBy('users.name', $sortOrder)
                    ->select('rekam_medis.*');
            } else {
                $query->orderBy('tanggal_berobat', $sortOrder);
            }
        }

        $daftarPasien = $query->get();

        return view('dokter.daftarPasien', compact('daftarPasien'));
    }

    public function detailPasien($id)
    {
        $dokterId = auth()->user()->dokter->id;

        $pasien = Pasien::findOrFail($id);
        
        $riwayatKonsultasi = RekamMedis::where('pasien_id', $id)
            ->where('dokter_id', $dokterId)
            ->get();

        return view('dokter.detailPasien', compact('pasien', 'riwayatKonsultasi'));
    }

    public function detailRekamMedis($id)
    {
        $dokterId = auth()->user()->dokter->id;

        $rekamMedis = RekamMedis::with(['pasien', 'dokter', 'resep.obat'])
            ->where('dokter_id', $dokterId)
            ->findOrFail($id);

        return view('dokter.detailRekamMedis', compact('rekamMedis'));
    }

    public function destroy($rekamMedisId)
    {
        $rekamMedis = RekamMedis::findOrFail($rekamMedisId);
        $rekamMedis->delete();

        return redirect()->route('dokter.dashboard')->with('status', 'Rekam medis berhasil dihapus.');
    }

}