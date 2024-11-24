<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RekamMedis;
use App\Models\PenjadwalanKonsultasi;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokterId = auth()->user()->id;
        $pasienSelesai = RekamMedis::with('pasien')
            ->where('dokter_id', $dokterId)
            ->latest('tanggal_berobat')
            ->take(5)
            ->get();

        $pasienKonsul = PenjadwalanKonsultasi::with('pasien')
            ->where('dokter_id', $dokterId)
            ->where('selesai', 'tidak')
            ->get();

        $totalJanji = PenjadwalanKonsultasi::with('pasien')
            ->where('dokter_id', $dokterId)->count();
    
        return view('dokter.dashboard', compact('pasienSelesai', 'pasienKonsul', 'totalJanji',));
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

            $query->orderBy($sortBy, $sortOrder);
        }

        $daftarPasien = $query->get();

        return view('dokter.daftarPasien', compact('daftarPasien'));
    }

    public function detailPasien($id)
    {
        // $rekamMedis = RekamMedis::with(['pasien', 'dokter', 'obat'])
        //     ->where('id', $id)
        //     ->where('created_by', auth()->id())
        //     ->firstOrFail();

        // return view('dokter.detailPasien', compact('rekamMedis'));
        return view('dokter.detailPasien');
    }
}