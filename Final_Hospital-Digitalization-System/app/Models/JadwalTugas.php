<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalTugas extends Model
{
    use HasFactory;

    protected $fillable = ['dokter_id', 'hari_tugas', 'jam_mulai', 'jam_selesai'];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}