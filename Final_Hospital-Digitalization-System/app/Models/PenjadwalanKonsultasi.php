<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjadwalanKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'penjadwalan_konsultasi';

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'tanggal_konsultasi',
        'konfirmasi',
        'selesai',
    ];

    /**
     * Relasi dengan model Pasien
     */
    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    /**
     * Relasi dengan model Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function isDokter()
    {
        return $this->dokter()->exists();
    }

    public function isPasien()
    {
        return $this->pasien()->exists();
    }
}