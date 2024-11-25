<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'resep';

    protected $fillable = [
        'rekam_medis_id',
        'obat_id',
        'dosis',
        'jumlah',
        'aturan_pakai',
        'keterangan',
        'created_by',
    ];

    /**
     * Relasi ke model RekamMedis.
     */
    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    /**
     * Relasi ke model Obat.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    /**
     * Relasi ke model Dokter (yang membuat resep).
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'created_by');
    }
}