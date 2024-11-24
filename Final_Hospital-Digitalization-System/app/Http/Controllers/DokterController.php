<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RekamMedis;
use App\Models\PenjadwalanKonsultasi;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokterId = auth()->user()->id;
        $pasienSelesai = RekamMedis::with(['pasien' => function ($query) {
                $query->where('role', 'pasien');
            }])
            ->where('dokter_id', $dokterId)
            ->latest('tanggal_berobat')
            ->take(5)
            ->get();

        // Janji yg sudah dikonfirmasi
        $pasienKonsul = PenjadwalanKonsultasi::with('pasien')
            ->where('dokter_id', $dokterId)
            ->where('konfirmasi', 'ya')
            ->where('selesai', 'tidak')
            ->get();

        $totalJanji = PenjadwalanKonsultasi::with('pasien')
            ->where('konfirmasi', 'ya')
            ->where('dokter_id', $dokterId)->count();

        // Janji yg belum dikonfirmasi
        $pasienMintaKonsul = PenjadwalanKonsultasi::with('pasien')
            ->where('dokter_id', $dokterId)
            ->where('konfirmasi', 'tidak')
            ->get();

        $totalMintaJanji = PenjadwalanKonsultasi::with('pasien')
            ->where('konfirmasi', 'tidak')
            ->where('dokter_id', $dokterId)->count();
    
        return view('dokter.dashboard', compact(
            'pasienSelesai', 'pasienKonsul', 'totalJanji', 'pasienMintaKonsul', 'totalMintaJanji'));
    }

    public function daftarPasien(Request $request)
    {
        $dokterId = auth()->id();

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
        $dokterId = auth()->id();

        $pasien = User::where('id', $id)
            ->where('role', 'pasien')
            ->firstOrFail();
        
        $riwayatKonsultasi = RekamMedis::where('pasien_id', $id)
            ->where('dokter_id', $dokterId)
            ->get();

        return view('dokter.detailPasien', compact('pasien', 'riwayatKonsultasi'));
    }

    public function detailRekamMedis($id)
    {
        $dokterId = auth()->id();

        $rekamMedis = RekamMedis::with('pasien')
            ->where('dokter_id', $dokterId)
            ->findOrFail($id);

        return view('dokter.detailRekamMedis', compact('rekamMedis'));
    }

}