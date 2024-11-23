<?php

namespace Database\Factories;

use App\Models\TindakanMedis;
use App\Models\User;
use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TindakanMedisFactory extends Factory
{
    protected $model = TindakanMedis::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Ambil Dokter dan Pasien secara acak dari database
        $dokterId = Dokter::pluck('id')->toArray();
        $pasienId = User::where('role', 'pasien')->pluck('id')->toArray();

        return [
            'pasien_id' => $this->faker->randomElement($pasienId),
            'dokter_id' => $this->faker->randomElement($dokterId),
            'deskripsi' => 'Pemeriksaan kesehatan rutin',
            'tanggal' => Carbon::today()->subDays(rand(0, 20))->addDays(rand(0, 10)),
            'notifikasi' => rand(0, 1),
        ];
    }
}