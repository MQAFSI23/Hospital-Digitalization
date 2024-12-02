<?php

namespace App\Http\Controllers;

use App\Models\PenjadwalanKonsultasi;
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

    public function janjiKonsultasiStore(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'tanggal_konsultasi' => 'required|date|after_or_equal:today|before_or_equal:'.now()->addWeeks(2)->toDateString(),
        ]);

        $pasienId = auth()->user()->pasien->id;
        $existingAppointment = PenjadwalanKonsultasi::where('pasien_id', $pasienId)
            ->where('status', '!=', 'selesai')
            ->exists();

        $namaDokter = Dokter::findOrFail($request->dokter_id)->user->name;
    
        if ($existingAppointment) {
            return back()->with('error', 'Anda memiliki janji konsultasi dengan ' . $namaDokter . ' yang belum selesai.');
        }

        $tanggalKonsultasi = Carbon::createFromFormat('d-m-Y', $request->tanggal_konsultasi)->format('Y-m-d');

        PenjadwalanKonsultasi::create([
            'pasien_id' => $pasienId,
            'dokter_id' => $request->dokter_id,
            'tanggal_konsultasi' => $tanggalKonsultasi,
            'status' => 'belum',
        ]);

        return redirect()->route('pasien.dashboard')->with('status', 'Janji konsultasi berhasil dibuat.');
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

        for ($i = 0; $i < 14; $i++) {
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
