<?php

namespace App\Http\Controllers;

use App\Models\JadwalTugas;

// Mendapatkan semua jadwal tugas yang berhubungan dengan dokter
$jadwalTugasDokter = JadwalTugas::dokter()->get();