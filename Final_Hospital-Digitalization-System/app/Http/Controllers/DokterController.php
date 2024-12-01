<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Pasien;
use App\Models\RekamMedis;
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
            'tindakan' => 'required|string',
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
            'tindakan' => $request->tindakan,
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

        $penjadwalan->status = 'selesai';
        $penjadwalan->save();

        $message = empty($request->obat_id) 
            ? 'Diagnosa berhasil disimpan. Tidak ada resep yang diberikan.'
            : 'Resep dan diagnosa berhasil disimpan.';
        
        return redirect()->route('dokter.dashboard')->with('status', $message);
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