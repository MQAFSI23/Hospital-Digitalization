<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'tanggal_lahir', 'jenis_kelamin',
    ];

    /**
     * Relasi untuk mendapatkan feedback yang diberikan oleh pasien (untuk dokter)
     */
    public function feedbackDokter()
    {
        return $this->hasMany(Feedback::class, 'dokter_id');
    }

    /**
     * Relasi untuk mendapatkan feedback yang diberikan oleh dokter (untuk pasien)
     */
    public function feedbackPasien()
    {
        return $this->hasMany(Feedback::class, 'pasien_id');
    }

    // Relasi ke model Dokter
    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'dokter_id');
    }

    /**
     * Relasi dengan model RekamMedis sebagai Pasien
     */
    public function rekamMedisSebagaiPasien()
    {
        return $this->hasMany(RekamMedis::class, 'pasien_id');
    }

    /**
     * Relasi dengan model RekamMedis sebagai Dokter
     */
    public function rekamMedisSebagaiDokter()
    {
        return $this->hasMany(RekamMedis::class, 'dokter_id');
    }

    /**
     * Relasi dengan tabel PenjadwalanKonsultasi untuk Pasien.
     */
    public function penjadwalanKonsultasi()
    {
        return $this->hasMany(PenjadwalanKonsultasi::class, 'id_pasien');
    }

    /**
     * Relasi dengan tabel PenjadwalanKonsultasi untuk Dokter.
     */
    public function konsultasiDokter()
    {
        return $this->hasMany(PenjadwalanKonsultasi::class, 'id_dokter');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
