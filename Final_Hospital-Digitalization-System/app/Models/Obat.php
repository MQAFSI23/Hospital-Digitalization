<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'nama_obat', 'deskripsi', 'tipe_obat', 'stok', 'gambar_obat', 'kedaluwarsa', 'status_kedaluwarsa',
    ];

    /**
     * Menentukan status kedaluwarsa obat
     */
    public function updateStatusKedaluwarsa()
    {
        if (Carbon::now()->gt(Carbon::parse($this->kedaluwarsa))) {
            $this->status_kedaluwarsa = 'kedaluwarsa';
        } else {
            $this->status_kedaluwarsa = 'belum kedaluwarsa';
        }
        $this->save();
    }

    /**
     * Relasi n-to-n dengan RekamMedis
     */
    public function resep()
    {
        return $this->hasMany(Resep::class, 'obat_id');
    }    

    public function logObat()
    {
        return $this->hasOne(LogObat::class, 'obat_id');
    }
}