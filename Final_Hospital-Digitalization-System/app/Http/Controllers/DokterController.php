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
        $obats = Obat::all();

        $pasienSelesai = RekamMedis::with('pasien')
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal_berobat', Carbon::today())
            ->get();
            // ->whereIn(DB::raw('DAYOFWEEK(tanggal_berobat)'), $this->getHariTugasAsDayOfWeek($jadwalDokter))

        $totalPasienSelesai = $pasienSelesai->count();

        $pasienKonsul = PenjadwalanKonsultasi::with('pasien')
            ->where('status', 'belum')
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal_konsultasi', Carbon::today())
            ->get();
            // ->whereIn(DB::raw('DAYOFWEEK(tanggal_konsultasi)'), $this->getHariTugasAsDayOfWeek($jadwalDokter))
    
        $pasienKonsul = $pasienKonsul->map(function ($penjadwalan) {
            $penjadwalan->rekamMedis = RekamMedis::where('pasien_id', $penjadwalan->pasien->id)
                                                ->whereDate('tanggal_berobat', Carbon::today())
                                                ->first();
            return $penjadwalan;
        });

        $totalKonsul = $pasienKonsul->count();
        
        return view('dokter.dashboard', compact(
            'pasienSelesai', 'obats', 'pasienKonsul', 'totalKonsul', 'totalPasienSelesai', 'jadwalDokter'));
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

    public function selesai(Request $request, RekamMedis $rekamMedis, PenjadwalanKonsultasi $penjadwalan)
    {
        $request->validate([
            'tindakan' => 'required|string',
            'diagnosa' => 'required|string',
            'tanggal_berobat' => 'required|date',
            'obat_id.*' => 'exists:obat,id',  
            'dosis.*' => 'required|string',   
            'jumlah.*' => 'required|integer|min:1', 
        ]);
        
        $rekamMedis->update([
            'tindakan' => $request->tindakan,
            'diagnosa' => $request->diagnosa,
            'tanggal_berobat' => $request->tanggal_berobat,
        ]);
        
        foreach ($request->obat_id as $key => $obatId) {
            Resep::create([
                'rekam_medis_id' => $rekamMedis->id,
                'obat_id' => $obatId,
                'dosis' => $request->dosis[$key],
                'jumlah' => $request->jumlah[$key],
                'aturan_pakai' => $request->aturan_pakai[$key],
                'status_pengambilan' => 'belum diambil',
                'created_by' => auth()->id(),
            ]);
        }
    
        return redirect()->route('dokter.dashboard')->with('status', 'Resep dan diagnosa berhasil disimpan.');
    }    

    public function daftarPasien(Request $request)
    {
        $dokterId = auth()->user()->dokter->id;

        $query = RekamMedis::with('pasien')
            ->where('dokter_id', $dokterId);

        if ($request->filled('search')) {
            $query->whereHas('pasien', function ($q) use ($request) {
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
            $sortBy = $request->sort_by === 'name' ? 'pasien.name' : 'tanggal_berobat';
            $sortOrder = $request->sort_order ?? 'asc';

            if ($sortBy === 'pasien.name') {
                $query->join('users as pasien', 'rekam_medis.pasien_id', '=', 'pasien.id')
                    ->orderBy('pasien.name', $sortOrder)
                    ->select('rekam_medis.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
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

}