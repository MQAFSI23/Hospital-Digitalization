<?php

namespace Database\Factories;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    protected $model = Dokter::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Ambil user yang berperan sebagai 'dokter'
        $dokterUser = User::where('role', 'dokter')->inRandomOrder()->first();

        // Jika tidak ada user dengan role dokter, buat exception
        if (!$dokterUser) {
            throw new \Exception('Tidak ada pengguna dengan peran dokter ditemukan.');
        }

        // Tentukan jenis dokter (umum atau spesialis)
        $jenisDokter = $this->faker->randomElement(['umum', 'spesialis']);

        // Jika jenis dokter adalah spesialis, tentukan spesialisasi
        $spesialisasi = null;
        if ($jenisDokter === 'spesialis') {
            $specialties = ['kardiologi', 'neurologi', 'gastroenterologi', 'pediatri', 'pulmonologi'];
            $spesialisasi = $this->faker->randomElement($specialties);
        }

        // Pastikan spesialisasi diberikan jika jenis dokter adalah spesialis
        if ($jenisDokter === 'spesialis' && !$spesialisasi) {
            throw new \Exception('Spesialisasi wajib diberikan untuk dokter spesialis.');
        }

        return [
            'user_id' => $dokterUser->id,  // Mengambil ID user yang memiliki peran 'dokter'
            'jenis_dokter' => $jenisDokter,
            'spesialisasi' => $spesialisasi,
        ];
    }

}