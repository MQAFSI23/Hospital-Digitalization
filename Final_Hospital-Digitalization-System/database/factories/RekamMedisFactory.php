<?php

namespace Database\Factories;

use App\Models\RekamMedis;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Resep;
use Illuminate\Database\Eloquent\Factories\Factory;

class RekamMedisFactory extends Factory
{
    protected $model = RekamMedis::class;

    public function definition()
    {
        $dokter = Dokter::inRandomOrder()->first()->id;
        
        return [
            'pasien_id' => User::where('role', 'pasien')->inRandomOrder()->first()->id,
            'dokter_id' => $dokter,
            'tindakan' => $this->faker->sentence(),
            'diagnosa' => $this->faker->sentence(),
            'tanggal_berobat' => $this->faker->date(),
            'created_by' => $dokter,
        ];
    }

    public function withResep($jumlahResep = 3)
    {
        return $this->has(
            Resep::factory()->count($jumlahResep),
            'resep'
        );
    }
}