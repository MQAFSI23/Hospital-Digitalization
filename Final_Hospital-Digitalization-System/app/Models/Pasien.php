<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'berat_badan',
        'tinggi_badan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsultasiPasien()
    {
        return $this->hasMany(PenjadwalanKonsultasi::class, 'pasien_id');
    }

    public function rekamMedisPasien()
    {
        return $this->hasMany(RekamMedis::class, 'pasien_id');
    }

    public function feedbackPasien()
    {
        return $this->hasMany(Feedback::class, 'pasien_id');
    }

}