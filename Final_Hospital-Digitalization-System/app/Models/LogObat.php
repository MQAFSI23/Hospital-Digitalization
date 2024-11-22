<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogObat extends Model
{
    use HasFactory;

    protected $table = 'log_obat';

    protected $fillable = [
        'obat_id',
        'status',
        'jumlah',
        'tanggal_log',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}