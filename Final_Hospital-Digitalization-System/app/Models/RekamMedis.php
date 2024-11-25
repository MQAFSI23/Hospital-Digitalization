<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'tindakan',
        'diagnosa',
        'tanggal_berobat',
        'created_by',
    ];

    /**
     * Relasi dengan model Pasien (users yang memiliki peran Pasien)
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Relasi dengan model Dokter (users yang memiliki peran Dokter)
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    /**
     * Relasi dengan model Obat
     */
    public function resep()
    {
        return $this->hasMany(Resep::class, 'rekam_medis_id');
    }
    
}