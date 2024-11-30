<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokter';

    protected $fillable = [
        'user_id',
        'jenis_dokter',
        'spesialisasi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwalTugas()
    {
        return $this->hasMany(JadwalTugas::class, 'dokter_id');
    }

    public function cuti()
    {
        return $this->hasMany(CutiDokter::class, 'dokter_id');
    }

    public function resepDibuat()
    {
        return $this->hasMany(Resep::class, 'created_by');
    }

    public function konsultasiDokter()
    {
        return $this->hasMany(PenjadwalanKonsultasi::class, 'dokter_id');
    }

    public function rekamMedisDokter()
    {
        return $this->hasMany(RekamMedis::class, 'dokter_id');
    }

    public function feedbackDokter()
    {
        return $this->hasMany(Feedback::class, 'dokter_id');
    }

}