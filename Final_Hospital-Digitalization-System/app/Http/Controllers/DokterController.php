<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PenjadwalanKonsultasi;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokterId = auth()->user()->id;

        $jumlahPasienHariIni = PenjadwalanKonsultasi::where('dokter_id', $dokterId)
            ->whereDate('tanggal_konsultasi', Carbon::today())
            ->count();

        return view('dokter.dashboard', compact('jumlahPasienHariIni'));
    }
}