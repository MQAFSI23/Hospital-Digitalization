<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\Dokter;
use \Carbon\Carbon;
use App\Models\JadwalTugas;

class PasienController extends Controller
{
    public function dashboard(Request $request)
    {
        $notifikasi = Notifikasi::with('pasien.user')
            ->where('pasien_id', auth()->user()->pasien->id)
            ->where('status', false)
            ->orderBy('tanggal', 'desc')
            ->get();

        $dokterUmum = Dokter::where('jenis_dokter', 'umum')
            ->get();

        $dokterSpesialisasi = Dokter::where('jenis_dokter', 'spesialis')
            ->get()->groupBy('spesialisasi');

        $jumlahDokterUmum = $dokterUmum->count();
        
        $jumlahDokterSpesialis = $dokterSpesialisasi->map(function ($dokters) {
            return $dokters->count();
        });
    
        return view('pasien.dashboard', compact(
            'notifikasi', 'dokterUmum', 'dokterSpesialisasi', 'jumlahDokterUmum', 'jumlahDokterSpesialis'
        ));
    }

    public function getAvailableDates($dokterId)
    {
        $dokter = Dokter::findOrFail($dokterId)->load('jadwalTugas');
    
        $hariTugasDokter = $dokter->jadwalTugas->pluck('hari_tugas')->toArray();
    
        $hariTugasDokterFormatted = array_map(fn($hari) => match (strtolower(trim($hari))) {
            'senin' => 'Monday',
            'selasa' => 'Tuesday',
            'rabu' => 'Wednesday',
            'kamis' => 'Thursday',
            'jumat' => 'Friday',
            'sabtu' => 'Saturday',
            default => null
        }, $hariTugasDokter);
    
        $hariTugasDokterFormatted = array_filter($hariTugasDokterFormatted);
    
        $today = Carbon::today();
        $availableDates = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            if (in_array($date->format('l'), $hariTugasDokterFormatted)) {
                $formattedDate = $date->format('d-m-Y');
                $dayName = match ($date->format('l')) {
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                };
                $availableDates[] = "$formattedDate ($dayName)";
            }
        }
        \Log::info('Available dates:', $availableDates);
    
        return response()->json(['availableDates' => $availableDates]);
    }        

    public function detailNotifikasi($id)
    {
        $notifikasi = Notifikasi::with(['pasien.user', 'resep.obat'])->findOrFail($id);
    
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
