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
        'status',
        'tanggal_konsultasi',
    ];

    /**
     * Relasi dengan model Pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Relasi dengan model Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
    
}