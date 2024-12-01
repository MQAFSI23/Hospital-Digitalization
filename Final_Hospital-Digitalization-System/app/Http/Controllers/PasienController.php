<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;

class PasienController extends Controller
{
    public function dashboard()
    {
        $notifikasi = Notifikasi::with('pasien.user')
        ->where('status', false)
        ->orderBy('tanggal', 'desc')
        ->get();

        return view('pasien.dashboard', compact('notifikasi'));
    }

    public function detailNotifikasi($id)
    {
        $notifikasi = Notifikasi::with(['pasien.user', 'pasien.rekamMedisPasien.resep.obat'])->findOrFail($id);
    
        $notifikasi->update(['status' => true]);
    
        return view('pasien.detailNotifikasi', compact('notifikasi'));
    }    

    public function semuaNotifikasi()
    {
        $pasienId = auth()->user()->pasien->id;
        $notifikasi = Notifikasi::where('pasien_id', $pasienId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pasien.notifikasi', compact('notifikasi'));
    }

}
