<?php

namespace App\Http\Controllers;

use App\Models\JadwalTugas;
use App\Models\User;
use App\Models\PenjadwalanKonsultasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $jumlahPengguna = User::count();
        $jumlahDokterBertugas = JadwalTugas::where('hari_tugas', Carbon::now()->isoFormat('dddd'))->count();
        $jumlahPasienHariIni = PenjadwalanKonsultasi::whereDate('tanggal_konsultasi', Carbon::today())->count();
        $penggunaTerbaru = User::where('created_at', '>=', Carbon::now()->subMonth())->get();
    
        return view('admin.dashboard', compact('jumlahPengguna', 'jumlahDokterBertugas', 'jumlahPasienHariIni', 'penggunaTerbaru'));
    }

    public function daftarPengguna(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $fromDate = Carbon::parse($request->date_from);
            $toDate = Carbon::parse($request->date_to);
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } else {
            if ($request->filled('date_from')) {
                $fromDate = Carbon::parse($request->date_from);
                $query->where('created_at', '>=', $fromDate);
            }
        
            if ($request->filled('date_to')) {
                $toDate = Carbon::parse($request->date_to);
                $query->where('created_at', '<=', $toDate);
            }
        }

        $daftarPengguna = $query->get();
        
        return view('admin.daftarPengguna', compact('daftarPengguna'));
    }

}